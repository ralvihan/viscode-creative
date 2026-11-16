<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\User;
use App\Services\Workflow\QuotationWorkflowService;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('quotations') && ! auth()->user()->canAccessModule('orders'), 403, 'Anda tidak memiliki hak akses modul Sales & Orders.');

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

    public function store(Request $request, QuotationWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('quotations'), 403, 'Anda tidak memiliki hak akses.');

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string|max:255',
            'scope_description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required_without:price|nullable|numeric|min:0',
            'estimated_deadline' => 'nullable|date',
            'valid_until' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'submit_action' => 'nullable|in:draft,send',
        ]);

        try {
            $quotation = $workflow->createQuotation($validated);

            if (($validated['submit_action'] ?? 'draft') === 'send') {
                return back()->with('success', 'Quotation '.$quotation->quotation_code.' berhasil dibuat dan dikirimkan kepada pelanggan!');
            }

            return back()->with('success', 'Draft Quotation '.$quotation->quotation_code.' berhasil disimpan!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['quotation_error' => $e->getMessage()]);
        }
    }

    public function send(Quotation $quotation, QuotationWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('quotations'), 403, 'Anda tidak memiliki hak akses.');

        $workflow->sendToCustomer($quotation);

        return back()->with('success', 'Quotation '.$quotation->quotation_code.' berhasil ditandai telah dikirim!');
    }

    public function accept(Quotation $quotation, QuotationWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('quotations'), 403, 'Anda tidak memiliki hak akses.');

        $workflow->customerAccept($quotation);

        return back()->with('success', 'Persetujuan pelanggan untuk Quotation '.$quotation->quotation_code.' berhasil dicatat!');
    }

    public function reject(Request $request, Quotation $quotation, QuotationWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('quotations'), 403, 'Anda tidak memiliki hak akses.');

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $workflow->customerReject($quotation, $request->rejection_reason);

        return back()->with('success', 'Penolakan Quotation '.$quotation->quotation_code.' berhasil dicatat.');
    }

    public function convertToOrder(Quotation $quotation, QuotationWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('orders'), 403, 'Anda tidak memiliki hak akses modul Pesanan.');

        $order = $workflow->convertToOrder($quotation);

        return back()->with('success', 'Quotation berhasil dikonversi menjadi Pesanan '.$order->order_code.'!');
    }
}
