<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Workflow\PaymentWorkflowService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('payments'), 403, 'Anda tidak memiliki hak akses modul Pembayaran.');

        $payments = Payment::with(['order.customer', 'recordedBy', 'verifiedBy', 'transaction'])
            ->latest('payment_date')
            ->paginate(15);

        $activeOrders = Order::with('customer')
            ->whereNotIn('status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->latest()
            ->get();

        return view('payments.index', compact('payments', 'activeOrders'));
    }

    public function store(Request $request, PaymentWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('payments'), 403, 'Anda tidak memiliki hak akses.');

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:1000',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:100',
            'proof_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($request->hasFile('proof_image')) {
            $validated['proof_image'] = $request->file('proof_image')->store('payments', 'public');
        }

        $payment = $workflow->recordPayment($order, $validated);

        return back()->with('success', 'Pembayaran '.$payment->payment_code.' berhasil dicatat (Menunggu Verifikasi Finance).');
    }

    public function verify(Payment $payment, PaymentWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isFinance() && ! auth()->user()->isSuperAdmin() && ! auth()->user()->isCeo(), 403, 'Hanya Finance atau Super Admin yang dapat memverifikasi pembayaran.');

        $workflow->verifyPayment($payment);

        return back()->with('success', 'Pembayaran '.$payment->payment_code.' telah diverifikasi dan dibukukan ke Buku Kas!');
    }

    public function reject(Request $request, Payment $payment, PaymentWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isFinance() && ! auth()->user()->isSuperAdmin() && ! auth()->user()->isCeo(), 403, 'Hanya Finance atau Super Admin yang dapat menolak pembayaran.');

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $workflow->rejectPayment($payment, $request->rejection_reason);

        return back()->with('success', 'Pembayaran '.$payment->payment_code.' telah ditolak.');
    }

    public function void(Request $request, Payment $payment, PaymentWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isFinance() && ! auth()->user()->isSuperAdmin() && ! auth()->user()->isCeo(), 403, 'Hanya Finance atau Super Admin yang dapat membatalkan pembayaran.');

        $request->validate([
            'void_reason' => 'required|string',
        ]);

        $workflow->voidPayment($payment, $request->void_reason);

        return back()->with('success', 'Pembayaran '.$payment->payment_code.' telah dibatalkan (VOID).');
    }
}
