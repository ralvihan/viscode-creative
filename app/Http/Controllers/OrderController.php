<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\Workflow\OrderWorkflowService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('orders') && ! auth()->user()->canAccessModule('quotations'), 403, 'Anda tidak memiliki hak akses modul Sales & Orders.');

        $orders = Order::with(['customer', 'service', 'pic', 'project', 'payments', 'quotation'])
            ->latest()
            ->paginate(15);

        $quotations = Quotation::with(['customer', 'service', 'creator', 'lead', 'order'])
            ->where('status', '!=', Quotation::STATUS_CONVERTED)
            ->latest()
            ->get();

        $allQuotations = Quotation::with(['customer', 'service', 'creator', 'lead', 'order'])
            ->latest()
            ->paginate(15);

        $services = Service::where('is_active', true)->get();
        $team = User::where('role', '!=', 'customer')->get();
        $customers = Customer::orderBy('name')->get();

        return view('orders.index', compact('orders', 'quotations', 'allQuotations', 'services', 'team', 'customers'));
    }

    public function store(Request $request, OrderWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('orders'), 403, 'Anda tidak memiliki hak akses.');

        $validated = $request->validate([
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_whatsapp' => 'required_without:customer_id|string|max:25',
            'customer_type' => 'nullable|in:umkm,organisasi,mahasiswa,individu,event_organizer,lainnya',
            'customer_id' => 'nullable|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'agreed_price' => 'required|numeric|min:0',
            'down_payment_required' => 'nullable|numeric|min:0',
            'brief_description' => 'required|string',
            'deadline' => 'nullable|date',
            'pic_id' => 'nullable|exists:users,id',
        ]);

        $order = $workflow->createOrder($validated);

        return back()->with('success', 'Pesanan '.$order->order_code.' berhasil dicatat! Status: PENDING_PAYMENT (Menunggu DP).');
    }

    public function update(Request $request, Order $order)
    {
        abort_if(! auth()->user()->canAccessModule('orders'), 403, 'Anda tidak memiliki hak akses.');

        // User cannot manually change status here; status changes via workflow or override!
        $validated = $request->validate([
            'agreed_price' => 'required|numeric|min:0',
            'down_payment_required' => 'nullable|numeric|min:0',
            'pic_id' => 'nullable|exists:users,id',
            'deadline' => 'nullable|date',
            'brief_description' => 'required|string',
            'result_file_url' => 'nullable|url',
        ]);

        $old = $order->toArray();
        $order->update($validated);
        $order->recalculatePaymentStatus();

        ActivityLogger::log('Orders', $order->order_code, 'UPDATE', $old, $order->toArray(), 'Pembaruan detail pesanan');

        return back()->with('success', 'Data pesanan '.$order->order_code.' berhasil diperbarui.');
    }

    public function recordDelivery(Request $request, Order $order, OrderWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('orders') && ! auth()->user()->canAccessModule('projects'), 403, 'Anda tidak memiliki hak akses.');

        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'result_file_url' => 'required|url',
            'delivery_notes' => 'nullable|string',
        ]);

        $workflow->recordDelivery($order, $validated);

        return back()->with('success', 'Hasil pekerjaan pesanan '.$order->order_code.' berhasil diserahkan ke klien!');
    }

    public function cancel(Request $request, Order $order, OrderWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('orders'), 403, 'Anda tidak memiliki hak akses.');

        $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $workflow->cancelOrder($order, $request->cancellation_reason);

        return back()->with('success', 'Pesanan '.$order->order_code.' berhasil dibatalkan.');
    }

    public function destroy(Order $order)
    {
        abort_if(! auth()->user()->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat menghapus data pesanan.');

        $old = $order->toArray();
        $code = $order->order_code;
        $order->delete();

        ActivityLogger::log('Orders', $code, 'DELETE', $old, null, 'Hapus pesanan oleh Super Admin');

        return back()->with('success', 'Pesanan '.$code.' berhasil dihapus.');
    }
}
