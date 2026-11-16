<?php

namespace App\Services\Workflow;

use App\Models\Order;
use App\Models\Quotation;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QuotationWorkflowService
{
    public function createQuotation(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $price = isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : (float) ($data['total_amount'] ?? 0);
            $discount = isset($data['discount_amount']) && $data['discount_amount'] !== '' ? (float) $data['discount_amount'] : 0.0;

            if ($discount > $price && $price > 0) {
                throw new InvalidArgumentException('Nominal diskon tidak boleh melebihi harga pekerjaan.');
            }

            $totalAmount = isset($data['price']) && $data['price'] !== ''
                ? max(0, $price - $discount)
                : (float) ($data['total_amount'] ?? max(0, $price - $discount));

            // Prevent duplicate active quotations for the same lead
            if (! empty($data['lead_id'])) {
                $existingActive = Quotation::where('lead_id', $data['lead_id'])
                    ->whereIn('status', [Quotation::STATUS_SENT, Quotation::STATUS_ACCEPTED, Quotation::STATUS_CONVERTED])
                    ->first();

                if ($existingActive) {
                    throw new InvalidArgumentException('Lead ini sudah memiliki penawaran aktif ('.$existingActive->quotation_code.').');
                }

                // If a DRAFT quotation already exists for this lead, update it instead of creating a duplicate
                $existingDraft = Quotation::where('lead_id', $data['lead_id'])
                    ->where('status', Quotation::STATUS_DRAFT)
                    ->first();

                if ($existingDraft) {
                    $oldDraft = $existingDraft->toArray();
                    $existingDraft->update([
                        'customer_id' => $data['customer_id'],
                        'service_id' => $data['service_id'],
                        'title' => $data['title'],
                        'scope_description' => $data['scope_description'],
                        'price' => $price,
                        'discount_amount' => $discount,
                        'total_amount' => $totalAmount,
                        'valid_until' => $data['valid_until'],
                        'estimated_deadline' => $data['estimated_deadline'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]);

                    ActivityLogger::log('Quotations', $existingDraft->quotation_code, 'UPDATE', $oldDraft, $existingDraft->toArray(), 'Pembaruan draft quotation dari prospek');

                    if (($data['submit_action'] ?? 'draft') === 'send') {
                        return $this->sendToCustomer($existingDraft);
                    }

                    return $existingDraft;
                }
            }

            $prefix = 'VSC-QUO-'.Carbon::now()->format('Ym').'-';
            $count = Quotation::where('quotation_code', 'like', $prefix.'%')->count() + 1;
            $quoCode = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'quotation_code' => $quoCode,
                'lead_id' => $data['lead_id'] ?? null,
                'customer_id' => $data['customer_id'],
                'service_id' => $data['service_id'],
                'title' => $data['title'],
                'scope_description' => $data['scope_description'],
                'price' => $price,
                'discount_amount' => $discount,
                'total_amount' => $totalAmount,
                'valid_until' => $data['valid_until'],
                'estimated_deadline' => $data['estimated_deadline'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => Quotation::STATUS_DRAFT,
                'created_by' => Auth::id() ?? $data['created_by'],
            ]);

            ActivityLogger::log('Quotations', $quotation->quotation_code, 'CREATE', null, $quotation->toArray(), 'Quotation baru dibuat (Draft)');

            if (($data['submit_action'] ?? 'draft') === 'send') {
                return $this->sendToCustomer($quotation);
            }

            return $quotation;
        });
    }

    public function sendToCustomer(Quotation $quotation): Quotation
    {
        if ($quotation->status !== Quotation::STATUS_DRAFT) {
            throw new InvalidArgumentException('Hanya quotation status DRAFT yang dapat dikirim.');
        }

        $old = $quotation->toArray();
        $quotation->status = Quotation::STATUS_SENT;
        $quotation->save();

        if ($quotation->lead) {
            app(LeadWorkflowService::class)->markQuotationSent($quotation->lead);
        }

        ActivityLogger::log('Quotations', $quotation->quotation_code, 'STATUS_CHANGE', $old, $quotation->toArray(), 'Quotation resmi dikirim kepada pelanggan (DRAFT -> SENT)');

        return $quotation;
    }

    public function customerAccept(Quotation $quotation): Quotation
    {
        if (! in_array($quotation->status, [Quotation::STATUS_SENT, Quotation::STATUS_DRAFT], true)) {
            throw new InvalidArgumentException('Status quotation tidak valid untuk disetujui.');
        }

        $old = $quotation->toArray();
        $quotation->status = Quotation::STATUS_ACCEPTED;
        $quotation->save();

        ActivityLogger::log('Quotations', $quotation->quotation_code, 'STATUS_CHANGE', $old, $quotation->toArray(), 'Pelanggan menyetujui penawaran quotation');

        return $quotation;
    }

    public function customerReject(Quotation $quotation, string $reason): Quotation
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan penolakan quotation wajib diisi.');
        }

        $old = $quotation->toArray();
        $quotation->status = Quotation::STATUS_REJECTED;
        $quotation->rejection_reason = $reason;
        $quotation->save();

        if ($quotation->lead) {
            app(LeadWorkflowService::class)->markLost($quotation->lead, 'Quotation ditolak: '.$reason);
        }

        ActivityLogger::log('Quotations', $quotation->quotation_code, 'STATUS_CHANGE', $old, $quotation->toArray(), 'Pelanggan menolak quotation: '.$reason);

        return $quotation;
    }

    public function convertToOrder(Quotation $quotation): Order
    {
        if ($quotation->status === Quotation::STATUS_CONVERTED) {
            throw new InvalidArgumentException('Quotation ini sudah pernah dikonversi menjadi pesanan.');
        }

        return DB::transaction(function () use ($quotation) {
            $prefix = 'VSC-ORD-'.Carbon::now()->format('Ym').'-';
            $count = Order::where('order_code', 'like', $prefix.'%')->count() + 1;
            $orderCode = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            $downPayment = (float) $quotation->total_amount * 0.5; // default 50% DP

            $order = Order::create([
                'order_code' => $orderCode,
                'customer_id' => $quotation->customer_id,
                'quotation_id' => $quotation->id,
                'service_id' => $quotation->service_id,
                'brief_description' => $quotation->title."\n".$quotation->scope_description,
                'agreed_price' => $quotation->total_amount,
                'down_payment_required' => $downPayment,
                'deadline' => $quotation->estimated_deadline ?? Carbon::now()->addDays(14),
                'status' => Order::STATUS_PENDING_PAYMENT,
                'payment_status' => Order::PAYMENT_UNPAID,
            ]);

            $oldQuo = $quotation->toArray();
            $quotation->status = Quotation::STATUS_CONVERTED;
            $quotation->converted_order_id = $order->id;
            $quotation->save();

            if ($quotation->lead) {
                app(LeadWorkflowService::class)->markWon($quotation->lead);
            }

            ActivityLogger::log('Quotations', $quotation->quotation_code, 'CONVERT', $oldQuo, $quotation->toArray(), 'Quotation dikonversi menjadi Pesanan '.$order->order_code);
            ActivityLogger::log('Orders', $order->order_code, 'CREATE', null, $order->toArray(), 'Pesanan dibuat otomatis dari konversi Quotation '.$quotation->quotation_code);

            return $order;
        });
    }
}
