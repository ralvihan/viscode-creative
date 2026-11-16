<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Services\Workflow\PurchaseWorkflowService;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('purchases'), 403, 'Anda tidak memiliki hak akses modul Pengajuan Pembelian.');

        $purchases = PurchaseRequest::with(['requester', 'approver', 'transaction'])
            ->latest()
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function store(Request $request, PurchaseWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('purchases'), 403, 'Anda tidak memiliki hak akses.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'purpose' => 'required|string',
            'estimated_amount' => 'required|numeric|min:1000',
        ]);

        $purchase = $workflow->createRequest($validated);

        return back()->with('success', 'Pengajuan pembelian '.$purchase->request_code.' berhasil diajukan!');
    }

    public function approve(Request $request, PurchaseRequest $purchase, PurchaseWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isCeo() && ! auth()->user()->isSuperAdmin(), 403, 'Hanya CEO atau Super Admin yang dapat menyetujui pengajuan pembelian.');

        $workflow->approve($purchase, $request->approval_notes);

        return back()->with('success', 'Pengajuan '.$purchase->request_code.' berhasil disetujui!');
    }

    public function reject(Request $request, PurchaseRequest $purchase, PurchaseWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isCeo() && ! auth()->user()->isSuperAdmin(), 403, 'Hanya CEO atau Super Admin yang dapat menolak pengajuan pembelian.');

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $workflow->reject($purchase, $request->rejection_reason);

        return back()->with('success', 'Pengajuan '.$purchase->request_code.' telah ditolak.');
    }

    public function recordPurchase(Request $request, PurchaseRequest $purchase, PurchaseWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('purchases'), 403, 'Anda tidak memiliki hak akses.');

        $request->validate([
            'actual_amount' => 'required|numeric|min:1000',
            'receipt_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('receipt_proof')) {
            $proofPath = $request->file('receipt_proof')->store('purchases', 'public');
        }

        $workflow->recordPurchase($purchase, (float) $request->actual_amount, $proofPath);

        return back()->with('success', 'Pembelian '.$purchase->request_code.' telah dicatat (Menunggu Buku Kas Finance).');
    }

    public function finalize(Request $request, PurchaseRequest $purchase, PurchaseWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isFinance() && ! auth()->user()->isSuperAdmin() && ! auth()->user()->isCeo(), 403, 'Hanya Finance atau Super Admin yang dapat membukukan pengeluaran pembelian.');

        $validated = $request->validate([
            'category' => 'nullable|string|max:100',
            'transaction_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
        ]);

        $workflow->finalizeExpense($purchase, $validated);

        return back()->with('success', 'Pengeluaran pembelian '.$purchase->request_code.' resmi dibukukan ke Buku Kas!');
    }

    public function cancel(Request $request, PurchaseRequest $purchase, PurchaseWorkflowService $workflow)
    {
        $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $workflow->cancel($purchase, $request->cancellation_reason);

        return back()->with('success', 'Pengajuan '.$purchase->request_code.' berhasil dibatalkan.');
    }
}
