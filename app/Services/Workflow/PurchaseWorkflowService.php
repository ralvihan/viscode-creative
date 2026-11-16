<?php

namespace App\Services\Workflow;

use App\Models\PurchaseRequest;
use App\Models\Transaction;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PurchaseWorkflowService
{
    public function createRequest(array $data): PurchaseRequest
    {
        return DB::transaction(function () use ($data) {
            $prefix = 'VSC-PR-'.Carbon::now()->format('Ym').'-';
            $count = PurchaseRequest::where('request_code', 'like', $prefix.'%')->count() + 1;
            $code = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            $purchase = PurchaseRequest::create([
                'request_code' => $code,
                'requested_by' => Auth::id() ?? $data['requested_by'],
                'title' => $data['title'],
                'purpose' => $data['purpose'],
                'estimated_amount' => $data['estimated_amount'],
                'status' => PurchaseRequest::STATUS_PENDING,
            ]);

            ActivityLogger::log('Purchases', $purchase->request_code, 'CREATE', null, $purchase->toArray(), 'Pengajuan pembelian diajukan (Pending Persetujuan CEO)');

            return $purchase;
        });
    }

    public function approve(PurchaseRequest $request, ?string $notes = null): PurchaseRequest
    {
        if ($request->status !== PurchaseRequest::STATUS_PENDING) {
            throw new InvalidArgumentException('Hanya pengajuan berstatus PENDING yang dapat disetujui.');
        }

        $old = $request->toArray();
        $request->status = PurchaseRequest::STATUS_APPROVED;
        $request->approved_by = Auth::id();
        $request->approval_notes = $notes;
        $request->save();

        ActivityLogger::log('Purchases', $request->request_code, 'STATUS_CHANGE', $old, $request->toArray(), 'Pengajuan disetujui oleh CEO');

        return $request;
    }

    public function reject(PurchaseRequest $request, string $reason): PurchaseRequest
    {
        if ($request->status !== PurchaseRequest::STATUS_PENDING) {
            throw new InvalidArgumentException('Hanya pengajuan berstatus PENDING yang dapat ditolak.');
        }

        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan penolakan pengajuan wajib disertakan.');
        }

        $old = $request->toArray();
        $request->status = PurchaseRequest::STATUS_REJECTED;
        $request->approved_by = Auth::id();
        $request->rejection_reason = $reason;
        $request->save();

        ActivityLogger::log('Purchases', $request->request_code, 'STATUS_CHANGE', $old, $request->toArray(), 'Pengajuan ditolak oleh CEO: '.$reason);

        return $request;
    }

    public function recordPurchase(PurchaseRequest $request, float $actualAmount, ?string $receiptProof = null): PurchaseRequest
    {
        if ($request->status !== PurchaseRequest::STATUS_APPROVED) {
            throw new InvalidArgumentException('Hanya pengajuan yang telah disetujui (APPROVED) yang dapat dicatat pembeliaannya.');
        }

        if ($actualAmount <= 0) {
            throw new InvalidArgumentException('Nominal pengeluaran aktual harus lebih dari Rp 0.');
        }

        $old = $request->toArray();
        $request->status = PurchaseRequest::STATUS_PURCHASED;
        $request->actual_amount = $actualAmount;
        if ($receiptProof) {
            $request->receipt_proof = $receiptProof;
        }
        $request->save();

        ActivityLogger::log('Purchases', $request->request_code, 'STATUS_CHANGE', $old, $request->toArray(), 'Barang/layanan telah dibeli, menunggu pencatatan pengeluaran Finance');

        return $request;
    }

    public function finalizeExpense(PurchaseRequest $request, array $transactionData = []): PurchaseRequest
    {
        if ($request->status !== PurchaseRequest::STATUS_PURCHASED) {
            throw new InvalidArgumentException('Hanya pengajuan berstatus PURCHASED yang dapat difinalisasi pencatatan pengeluarannya.');
        }

        return DB::transaction(function () use ($request, $transactionData) {
            $old = $request->toArray();
            $request->status = PurchaseRequest::STATUS_COMPLETED;
            $request->save();

            // Record into transactions (Kas Keluar)
            $trxPrefix = 'TRX-'.Carbon::now()->format('Ym').'-';
            $count = Transaction::withTrashed()->where('transaction_code', 'like', $trxPrefix.'%')->count() + 1;
            $trxCode = $trxPrefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            Transaction::create([
                'transaction_code' => $trxCode,
                'purchase_request_id' => $request->id,
                'recorded_by' => Auth::id(),
                'type' => 'expense',
                'category' => $transactionData['category'] ?? 'Operasional & Produksi',
                'description' => 'Pembelian ('.$request->request_code.'): '.$request->title,
                'amount' => $request->actual_amount ?? $request->estimated_amount,
                'transaction_date' => $transactionData['transaction_date'] ?? date('Y-m-d'),
                'payment_method' => $transactionData['payment_method'] ?? 'Kas Operasional / Transfer',
                'proof_image' => $request->receipt_proof,
            ]);

            ActivityLogger::log('Purchases', $request->request_code, 'STATUS_CHANGE', $old, $request->toArray(), 'Pengeluaran resmi dicatat di Buku Kas oleh Finance');

            return $request;
        });
    }

    public function cancel(PurchaseRequest $request, string $reason): PurchaseRequest
    {
        if (in_array($request->status, [PurchaseRequest::STATUS_COMPLETED, PurchaseRequest::STATUS_CANCELLED], true)) {
            throw new InvalidArgumentException('Pengajuan ini tidak dapat dibatalkan.');
        }

        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan pembatalan wajib dicatat.');
        }

        $old = $request->toArray();
        $request->status = PurchaseRequest::STATUS_CANCELLED;
        $request->cancellation_reason = $reason;
        $request->save();

        ActivityLogger::log('Purchases', $request->request_code, 'STATUS_CHANGE', $old, $request->toArray(), 'Pengajuan dibatalkan: '.$reason);

        return $request;
    }
}
