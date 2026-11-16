<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MonthlyReport;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Workflow\ReportWorkflowService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function getMonthlyData($month, $year)
    {
        $incomes = Transaction::with('order.customer')
            ->where('type', 'income')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->orderBy('transaction_date')
            ->get();

        $expenses = Transaction::where('type', 'expense')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->orderBy('transaction_date')
            ->get();

        $totalIncome = (float) $incomes->sum('amount');
        $totalExpense = (float) $expenses->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $ordersIn = Order::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
        $ordersCompleted = Order::where('status', Order::STATUS_COMPLETED)->whereMonth('updated_at', $month)->whereYear('updated_at', $year)->count();
        $newCustomers = Customer::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();

        $periodName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return compact('incomes', 'expenses', 'totalIncome', 'totalExpense', 'netProfit', 'ordersIn', 'ordersCompleted', 'newCustomers', 'periodName', 'month', 'year');
    }

    public function index(Request $request, ReportWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('reports'), 403, 'Anda tidak memiliki hak akses modul Laporan.');

        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $report = MonthlyReport::where('month', $month)->where('year', $year)->first();
        if (! $report) {
            $report = $workflow->generateOrUpdateReport($month, $year);
        }

        $data = $this->getMonthlyData($month, $year);
        $data['report'] = $report;
        $data['allReports'] = MonthlyReport::orderBy('year', 'desc')->orderBy('month', 'desc')->get();

        return view('reports.index', $data);
    }

    public function generate(Request $request, ReportWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('reports'), 403, 'Anda tidak memiliki hak akses.');

        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $report = $workflow->generateOrUpdateReport($month, $year);

        return back()->with('success', 'Laporan bulanan '.$report->report_code.' berhasil disinkronisasi dengan transaksi aktual!');
    }

    public function submitReview(MonthlyReport $report, ReportWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isFinance() && ! auth()->user()->isSuperAdmin(), 403, 'Hanya Finance atau Super Admin yang dapat mengajukan laporan.');

        $workflow->submitForReview($report);

        return back()->with('success', 'Laporan '.$report->report_code.' berhasil diajukan untuk review CEO!');
    }

    public function approve(MonthlyReport $report, ReportWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isCeo() && ! auth()->user()->isSuperAdmin(), 403, 'Hanya CEO atau Super Admin yang dapat menyetujui laporan.');

        $workflow->approve($report);

        return back()->with('success', 'Laporan '.$report->report_code.' telah disetujui oleh CEO!');
    }

    public function finalize(MonthlyReport $report, ReportWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isFinance() && ! auth()->user()->isCeo() && ! auth()->user()->isSuperAdmin(), 403, 'Akses finalisasi tidak diizinkan.');

        $workflow->finalize($report);

        return back()->with('success', 'Laporan '.$report->report_code.' telah resmi difinalisasi dan dikunci!');
    }

    public function exportPdf(Request $request)
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $evaluation = $request->input('evaluation', 'Operasional berjalan lancar sesuai target bulanan kelompok.');

        $data = $this->getMonthlyData($month, $year);
        $data['evaluation'] = $evaluation;
        $data['report'] = MonthlyReport::where('month', $month)->where('year', $year)->first();

        $pdf = Pdf::loadView('reports.pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Bulanan-Viscode-'.$data['periodName'].'.pdf');
    }
}
