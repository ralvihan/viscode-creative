<?php

namespace App\Services\Workflow;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentWorkflowService
{
    public function recordPayment(Order $order, array $data): Payment
    {
        return DB::transaction(function () use ($order, $data) {
            $prefix = 'VSC-PAY-'.Carbon::now()->format('Ym').'-';
            $count = Payment::where('payment_code', 'like', $prefix.'%')->count() + 1;
            $paymentCode = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            $payment = Payment::create([
                'payment_code' => $paymentCode,
                'order_id' => $order->id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'] ?? date('Y-m-d'),
                'payment_method' => $data['payment_method'] ?? 'Transfer Bank / QRIS',
                'proof_image' => $data['proof_image'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => Payment::STATUS_PENDING,
                'recorded_by' => Auth::id() ?? $data['recorded_by'],
            ]);

            ActivityLogger::log('Payments', $payment->payment_code, 'CREATE', null, $payment->toArray(), 'Pencatatan pembayaran baru (Status: Pending Verifikasi Finance)');

            return $payment;
        });
    }

    public function verifyPayment(Payment $payment): Payment
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            throw new InvalidArgumentException('Hanya pembayaran berstatus PENDING yang dapat diverifikasi.');
        }

        return DB::transaction(function () use ($payment) {
            $old = $payment->toArray();
            $payment->status = Payment::STATUS_VERIFIED;
            $payment->verified_by = Auth::id();
            $payment->verified_at = now();
            $payment->save();

            // 1. Create financial transaction in cashbook
            $trxPrefix = 'TRX-'.Carbon::now()->format('Ym').'-';
            $count = Transaction::withTrashed()->where('transaction_code', 'like', $trxPrefix.'%')->count() + 1;
            $trxCode = $trxPrefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            $order = $payment->order;
            Transaction::create([
                'transaction_code' => $trxCode,
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'recorded_by' => Auth::id() ?? $payment->recorded_by,
                'type' => 'income',
                'category' => 'Pembayaran Pesanan '.$order->order_code,
                'description' => 'Pembayaran '.$payment->payment_code.' via '.$payment->payment_method.' ('.$order->customer->name.')',
                'amount' => $payment->amount,
                'transaction_date' => $payment->payment_date,
                'payment_method' => $payment->payment_method,
                'proof_image' => $payment->proof_image,
            ]);

            // 2. Recalculate Order Payment Status (UNPAID -> PARTIALLY_PAID -> PAID)
            $order->recalculatePaymentStatus();

            // 3. Trigger Order confirmation if DP requirement is met
            app(OrderWorkflowService::class)->checkAndTriggerConfirmation($order);

            // 4. Trigger Order completion if both deliverable/project and full payment are satisfied
            app(OrderWorkflowService::class)->checkAndTriggerOrderCompletion($order);

            ActivityLogger::log('Payments', $payment->payment_code, 'VERIFY', $old, $payment->toArray(), 'Finance memverifikasi pembayaran dan membukukan mutasi kas masuk');

            return $payment;
        });
    }

    public function rejectPayment(Payment $payment, string $reason): Payment
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan penolakan pembayaran wajib dicatat.');
        }

        if ($payment->status !== Payment::STATUS_PENDING) {
            throw new InvalidArgumentException('Hanya pembayaran berstatus PENDING yang dapat ditolak.');
        }

        $old = $payment->toArray();
        $payment->status = Payment::STATUS_REJECTED;
        $payment->rejection_reason = $reason;
        $payment->verified_by = Auth::id();
        $payment->verified_at = now();
        $payment->save();

        ActivityLogger::log('Payments', $payment->payment_code, 'REJECT', $old, $payment->toArray(), 'Finance menolak pembayaran: '.$reason);

        return $payment;
    }

    public function voidPayment(Payment $payment, string $reason): Payment
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan pembatalan/void pembayaran wajib dicatat.');
        }

        return DB::transaction(function () use ($payment, $reason) {
            $old = $payment->toArray();
            $payment->status = Payment::STATUS_VOIDED;
            $payment->void_reason = $reason;
            $payment->save();

            // Soft-delete corresponding transaction if exists
            if ($payment->transaction) {
                $payment->transaction->delete();
            }

            // Recalculate Order payment status
            $order = $payment->order;
            $order->recalculatePaymentStatus();

            ActivityLogger::log('Payments', $payment->payment_code, 'VOID', $old, $payment->toArray(), 'Pembayaran dibatalkan / void: '.$reason);

            return $payment;
        });
    }
}
