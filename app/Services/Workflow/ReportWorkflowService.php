<?php

namespace App\Services\Workflow;

use App\Models\Customer;
use App\Models\MonthlyReport;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReportWorkflowService
{
    public function generateOrUpdateReport(int $month, int $year): MonthlyReport
    {
        return DB::transaction(function () use ($month, $year) {
            $report = MonthlyReport::where('month', $month)->where('year', $year)->first();

            if ($report && $report->isLocked()) {
                throw new InvalidArgumentException('Laporan untuk periode ini sudah FINALIZED dan terkunci.');
            }

            // Real calculations based on transactions
            $incomes = Transaction::where('type', 'income')
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount');

            $expenses = Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount');

            $netProfit = $incomes - $expenses;

            $ordersIn = Order::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $ordersCompleted = Order::where('status', Order::STATUS_COMPLETED)
                ->whereMonth('updated_at', $month)
                ->whereYear('updated_at', $year)
                ->count();
            $newCustomers = Customer::whereMonth('created_at', $month)->whereYear('created_at', $year)->count();

            $reportCode = sprintf('REP-%04d%02d', $year, $month);

            if (! $report) {
                $report = MonthlyReport::create([
                    'report_code' => $reportCode,
                    'month' => $month,
                    'year' => $year,
                    'total_income' => $incomes,
                    'total_expense' => $expenses,
                    'net_profit' => $netProfit,
                    'total_orders_in' => $ordersIn,
                    'total_orders_completed' => $ordersCompleted,
                    'total_new_customers' => $newCustomers,
                    'status' => MonthlyReport::STATUS_DRAFT,
                    'submitted_by' => Auth::id(),
                ]);

                ActivityLogger::log('Reports', $report->report_code, 'CREATE', null, $report->toArray(), 'Draft laporan bulanan digenerate dari transaksi');
            } else {
                $old = $report->toArray();
                $report->update([
                    'total_income' => $incomes,
                    'total_expense' => $expenses,
                    'net_profit' => $netProfit,
                    'total_orders_in' => $ordersIn,
                    'total_orders_completed' => $ordersCompleted,
                    'total_new_customers' => $newCustomers,
                ]);

                ActivityLogger::log('Reports', $report->report_code, 'UPDATE', $old, $report->toArray(), 'Angka laporan bulanan disinkronkan dari transaksi');
            }

            return $report;
        });
    }

    public function submitForReview(MonthlyReport $report): MonthlyReport
    {
        if ($report->status !== MonthlyReport::STATUS_DRAFT) {
            throw new InvalidArgumentException('Hanya laporan berstatus DRAFT yang dapat diajukan untuk review.');
        }

        $old = $report->toArray();
        $report->status = MonthlyReport::STATUS_UNDER_REVIEW;
        $report->submitted_by = Auth::id();
        $report->save();

        ActivityLogger::log('Reports', $report->report_code, 'STATUS_CHANGE', $old, $report->toArray(), 'Finance mengajukan laporan bulanan untuk review CEO');

        return $report;
    }

    public function approve(MonthlyReport $report): MonthlyReport
    {
        if ($report->status !== MonthlyReport::STATUS_UNDER_REVIEW) {
            throw new InvalidArgumentException('Hanya laporan berstatus UNDER_REVIEW yang dapat disetujui.');
        }

        $old = $report->toArray();
        $report->status = MonthlyReport::STATUS_APPROVED;
        $report->approved_by = Auth::id();
        $report->save();

        ActivityLogger::log('Reports', $report->report_code, 'STATUS_CHANGE', $old, $report->toArray(), 'CEO menyetujui laporan bulanan');

        return $report;
    }

    public function finalize(MonthlyReport $report): MonthlyReport
    {
        if ($report->status !== MonthlyReport::STATUS_APPROVED) {
            throw new InvalidArgumentException('Hanya laporan berstatus APPROVED yang dapat difinalisasi.');
        }

        $old = $report->toArray();
        $report->status = MonthlyReport::STATUS_FINALIZED;
        $report->finalized_at = now();
        $report->save();

        ActivityLogger::log('Reports', $report->report_code, 'STATUS_CHANGE', $old, $report->toArray(), 'Laporan bulanan difinalisasi dan dikunci resmi');

        return $report;
    }
}
