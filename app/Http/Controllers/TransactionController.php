<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('financials'), 403, 'Anda tidak memiliki hak akses modul Keuangan / Kas.');

        $transactions = Transaction::with(['order.customer', 'recorder', 'payment', 'purchaseRequest'])
            ->latest('transaction_date')
            ->paginate(15);

        $orders = Order::with('customer')
            ->whereNotIn('status', [Order::STATUS_CANCELLED])
            ->latest()
            ->get();

        return view('transactions.index', compact('transactions', 'orders'));
    }

    public function store(Request $request)
    {
        abort_if(! auth()->user()->canAccessModule('financials'), 403, 'Anda tidak memiliki hak akses modul Keuangan / Kas.');

        $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'transaction_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'order_id' => 'nullable|exists:orders,id',
            'proof_image' => 'nullable|image|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('proofs', 'public');
        }

        $prefix = 'TRX-'.Carbon::now()->format('Ym').'-';
        $count = Transaction::withTrashed()->where('transaction_code', 'like', $prefix.'%')->count() + 1;
        $trxCode = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        $trx = Transaction::create([
            'transaction_code' => $trxCode,
            'order_id' => $request->type === 'income' ? $request->order_id : null,
            'recorded_by' => Auth::id(),
            'type' => $request->type,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'payment_method' => $request->payment_method,
            'proof_image' => $proofPath,
        ]);

        ActivityLogger::log('Financials', $trx->transaction_code, 'CREATE', null, $trx->toArray(), 'Pencatatan mutasi kas langsung');

        return back()->with('success', 'Transaksi '.$trxCode.' berhasil disimpan!');
    }

    public function destroy(Transaction $transaction)
    {
        abort_if(! auth()->user()->isFinance() && ! auth()->user()->isSuperAdmin(), 403, 'Akses tidak diizinkan.');

        $old = $transaction->toArray();
        $code = $transaction->transaction_code;
        $transaction->delete();

        ActivityLogger::log('Financials', $code, 'DELETE', $old, null, 'Transaksi diarsipkan / dibatalkan');

        return back()->with('success', 'Transaksi dibatalkan (diarsipkan).');
    }
}
