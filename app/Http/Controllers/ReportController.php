<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\MonthlyReport;
use App\Models\Order;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Transaction;
use App\Services\Workflow\ReportWorkflowService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Gather comprehensive monthly financial & operational metrics.
     *
     * @return array<string, mixed>
     */
    private function getMonthlyData(int $month, int $year): array
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
        $profitMargin = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 1) : 0.0;

        $leadsIn = Lead::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
        $quotationsCreated = Quotation::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
        $ordersIn = Order::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
        $ordersCompleted = Order::where('status', Order::STATUS_COMPLETED)
            ->whereMonth('updated_at', $month)
            ->whereYear('updated_at', $year)
            ->count();
        $activeProjects = Project::whereIn('status', [
            Project::STATUS_NOT_STARTED,
            Project::STATUS_IN_PROGRESS,
            Project::STATUS_REVIEW,
            Project::STATUS_REVISION,
        ])->count();
        $newCustomers = Customer::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();

        $outstandingReceivables = (float) Order::with('payments')
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->where('payment_status', '!=', Order::PAYMENT_PAID)
            ->get()
            ->sum(fn (Order $order): float => $order->remainingBalance());

        $completionRate = $ordersIn > 0 ? round(($ordersCompleted / $ordersIn) * 100, 1) : ($ordersCompleted > 0 ? 100.0 : 0.0);

        $periodName = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');

        return compact(
            'incomes',
            'expenses',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'profitMargin',
            'leadsIn',
            'quotationsCreated',
            'ordersIn',
            'ordersCompleted',
            'activeProjects',
            'newCustomers',
            'outstandingReceivables',
            'completionRate',
            'periodName',
            'month',
            'year'
        );
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
        abort_if(! auth()->user()->canAccessModule('reports'), 403, 'Anda tidak memiliki hak akses modul Laporan.');

        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $evaluation = trim((string) $request->input('evaluation', ''));
        if ($evaluation === '') {
            $evaluation = 'Operasional bisnis Viscode Creative berjalan dengan baik sesuai rencana kerja bulanan. Tim berfokus pada peningkatan kualitas layanan, ketepatan waktu penyelesaian proyek, serta pengelolaan arus kas usaha secara transparan.';
        }

        $data = $this->getMonthlyData($month, $year);
        $data['evaluation'] = $evaluation;
        $data['report'] = MonthlyReport::where('month', $month)->where('year', $year)->first();
        $data['printedAt'] = Carbon::now()->locale('id')->translatedFormat('d F Y, H:i').' WIB';

        $logoPath = public_path('assets/logo.png');
        $data['logoBase64'] = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('reports.pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Bulanan-Viscode-Creative-'.str_replace(' ', '-', $data['periodName']).'.pdf');
    }
}
