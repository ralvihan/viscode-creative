<?php

namespace App\Services\Workflow;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Project;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderWorkflowService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $customer = null;
            if (! empty($data['customer_id'])) {
                $customer = Customer::findOrFail($data['customer_id']);
            } else {
                $customer = Customer::firstOrCreate(
                    ['phone' => $data['customer_whatsapp'] ?? $data['phone']],
                    [
                        'name' => $data['customer_name'],
                        'type' => $data['customer_type'] ?? 'individu',
                    ]
                );
            }

            $prefix = 'VSC-ORD-'.Carbon::now()->format('Ym').'-';
            $count = Order::where('order_code', 'like', $prefix.'%')->count() + 1;
            $orderCode = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            $agreedPrice = (float) $data['agreed_price'];
            $downPayment = isset($data['down_payment_required']) && $data['down_payment_required'] !== ''
                ? (float) $data['down_payment_required']
                : ($agreedPrice * 0.5);

            $order = Order::create([
                'order_code' => $orderCode,
                'customer_id' => $customer->id,
                'service_id' => $data['service_id'],
                'pic_id' => $data['pic_id'] ?? null,
                'brief_description' => $data['brief_description'],
                'agreed_price' => $agreedPrice,
                'down_payment_required' => $downPayment,
                'deadline' => $data['deadline'] ?? null,
                'status' => Order::STATUS_PENDING_PAYMENT,
                'payment_status' => Order::PAYMENT_UNPAID,
            ]);

            ActivityLogger::log('Orders', $order->order_code, 'CREATE', null, $order->toArray(), 'Pesanan baru dicatat (Menunggu Pembayaran DP)');

            return $order;
        });
    }

    public function checkAndTriggerConfirmation(Order $order): bool
    {
        // Must be in PENDING_PAYMENT
        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return false;
        }

        $verifiedPaid = $order->totalVerifiedPayments();
        $requiredDp = (float) $order->down_payment_required;

        // If verified payment meets or exceeds DP requirement
        if ($verifiedPaid >= $requiredDp && $verifiedPaid > 0) {
            $old = $order->toArray();
            $order->status = Order::STATUS_CONFIRMED;
            $order->save();

            ActivityLogger::log('Orders', $order->order_code, 'STATUS_CHANGE', $old, $order->toArray(), 'Pembayaran DP terpenuhi ('.number_format($verifiedPaid, 0, ',', '.').' >= '.number_format($requiredDp, 0, ',', '.').'), Pesanan terkonfirmasi otomatis');

            return true;
        }

        return false;
    }

    public function markInProgress(Order $order): Order
    {
        if (! in_array($order->status, [Order::STATUS_CONFIRMED, Order::STATUS_PENDING_PAYMENT], true)) {
            throw new InvalidArgumentException('Pesanan tidak dalam status yang dapat dimulai pengerjaannya.');
        }

        $old = $order->toArray();
        $order->status = Order::STATUS_IN_PROGRESS;
        $order->save();

        ActivityLogger::log('Orders', $order->order_code, 'STATUS_CHANGE', $old, $order->toArray(), 'Proyek pesanan mulai dikerjakan');

        return $order;
    }

    public function recordDelivery(Order $order, array $data): Order
    {
        $old = $order->toArray();
        $order->delivery_date = $data['delivery_date'] ?? date('Y-m-d');
        if (! empty($data['result_file_url'])) {
            $order->result_file_url = $data['result_file_url'];
        }
        if (! empty($data['delivery_notes'])) {
            $order->delivery_notes = $data['delivery_notes'];
        }
        $order->save();

        ActivityLogger::log('Orders', $order->order_code, 'DELIVERY', $old, $order->toArray(), 'Hasil pekerjaan diserahkan ke klien (Deliverables: '.($order->result_file_url ?? 'Tercatat').')');

        // Check if both delivery/project and full payment are satisfied
        $this->checkAndTriggerOrderCompletion($order);

        return $order;
    }

    public function checkAndTriggerOrderCompletion(Order $order): bool
    {
        if (in_array($order->status, [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED], true)) {
            return false;
        }

        $projectCompleted = $order->project && $order->project->status === Project::STATUS_COMPLETED;
        $hasDelivery = ! empty($order->result_file_url) || ! empty($order->delivery_date);
        $isPaid = $order->payment_status === Order::PAYMENT_PAID;

        // An order completes when work is done/delivered AND payment is 100% paid
        if (($projectCompleted || $hasDelivery) && $isPaid) {
            $old = $order->toArray();
            $order->status = Order::STATUS_COMPLETED;
            $order->save();

            ActivityLogger::log('Orders', $order->order_code, 'STATUS_CHANGE', $old, $order->toArray(), 'Pesanan selesai sepenuhnya (Deliverable tuntas & Pembayaran Lunas 100%)');

            return true;
        }

        return false;
    }

    public function completeOrder(Order $order): Order
    {
        if ($order->status === Order::STATUS_COMPLETED) {
            return $order;
        }

        $old = $order->toArray();
        $order->status = Order::STATUS_COMPLETED;
        $order->save();

        ActivityLogger::log('Orders', $order->order_code, 'STATUS_CHANGE', $old, $order->toArray(), 'Pekerjaan proyek pesanan selesai & diverifikasi');

        return $order;
    }

    public function cancelOrder(Order $order, string $reason): Order
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan pembatalan pesanan wajib dicatat.');
        }

        $old = $order->toArray();
        $order->status = Order::STATUS_CANCELLED;
        $order->cancellation_reason = $reason;
        $order->save();

        ActivityLogger::log('Orders', $order->order_code, 'STATUS_CHANGE', $old, $order->toArray(), 'Pesanan dibatalkan: '.$reason);

        return $order;
    }
}
