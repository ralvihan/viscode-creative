<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Documentation;
use App\Models\Lead;
use App\Models\MonthlyReport;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\Task;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.sidebar', 'layouts.topbar', 'components.viscode-workspace-tabs'], function ($view): void {
            static $cachedNavCounts = null;

            if ($cachedNavCounts === null) {
                $cachedNavCounts = $this->resolveNavigationCounts();
            }

            $view->with('navCounts', $cachedNavCounts);
        });
    }

    /**
     * Compute real-time notification badge counts for Sidebar & Workspace Tabs.
     *
     * @return array<string, int>
     */
    private function resolveNavigationCounts(): array
    {
        $defaults = [
            'orders_active' => 0,
            'orders_waiting_payment' => 0,
            'quotations_active' => 0,
            'sales_orders_total_active' => 0,
            'leads_total_active' => 0,
            'leads_new' => 0,
            'customers_total' => 0,
            'services_total' => 0,
            'sidebar_sales_badge' => 0,
            'projects_active' => 0,
            'projects_new' => 0,
            'my_tasks_pending' => 0,
            'qc_review_pending' => 0,
            'documentations_total' => 0,
            'sidebar_operations_badge' => 0,
            'payments_pending' => 0,
            'purchases_active' => 0,
            'transactions_total' => 0,
            'reports_pending' => 0,
            'sidebar_finance_badge' => 0,
            'dashboard_attention' => 0,
        ];

        if (! Auth::check() || ! Schema::hasTable('orders')) {
            return $defaults;
        }

        $user = Auth::user();

        // 1. Sales & Orders counts (Pesanan berjalan / belum dikerjakan + Penawaran aktif + Prospek aktif)
        $ordersActive = Order::whereIn('status', [
            Order::STATUS_PENDING_PAYMENT,
            Order::STATUS_CONFIRMED,
            Order::STATUS_IN_PROGRESS,
        ])->count();

        $ordersWaitingPayment = Order::where('status', '!=', Order::STATUS_CANCELLED)
            ->where(function ($q): void {
                $q->where('status', Order::STATUS_PENDING_PAYMENT)
                    ->orWhere('payment_status', '!=', Order::PAYMENT_PAID);
            })->count();

        $quotationsActive = Quotation::whereIn('status', [
            Quotation::STATUS_DRAFT,
            Quotation::STATUS_SENT,
            Quotation::STATUS_ACCEPTED,
        ])->count();

        $leadsActive = Lead::whereIn('status', [
            Lead::STATUS_NEW,
            Lead::STATUS_REVIEWED,
            Lead::STATUS_FOLLOW_UP,
            Lead::STATUS_CONTACTED,
            Lead::STATUS_QUOTATION_SENT,
            Lead::STATUS_OFFERED,
        ])->count();

        $leadsNew = Lead::where('status', Lead::STATUS_NEW)->count();
        $customersTotal = Customer::count();
        $servicesTotal = Service::count();

        $salesOrdersTotalActive = $ordersActive + $quotationsActive;
        $sidebarSalesBadge = $ordersActive + $quotationsActive + $leadsActive;

        // 2. Operations (Projects, Tasks, QC) counts
        $projectsActive = Project::whereIn('status', [
            Project::STATUS_NOT_STARTED,
            Project::STATUS_IN_PROGRESS,
            Project::STATUS_REVIEW,
            Project::STATUS_REVISION,
        ])->count();

        $projectsNew = Project::where('status', Project::STATUS_NOT_STARTED)->count();

        $myTasksQuery = Task::where('status', '!=', Task::STATUS_DONE);
        if (! $user->isSuperAdmin() && ! $user->isCeo() && ! $user->isQc()) {
            $myTasksQuery->where('assigned_to', $user->id);
        }
        $myTasksPending = $myTasksQuery->count();

        $qcReviewPending = Project::where('status', Project::STATUS_REVIEW)->count()
            + Task::where('status', Task::STATUS_IN_REVIEW)->count();

        $documentationsTotal = Documentation::count();
        $sidebarOperationsBadge = $projectsActive;

        // 3. Finance & Reports counts
        $paymentsPending = Payment::where('status', Payment::STATUS_PENDING)->count();
        $purchasesActive = PurchaseRequest::whereIn('status', [
            PurchaseRequest::STATUS_PENDING,
            PurchaseRequest::STATUS_APPROVED,
            PurchaseRequest::STATUS_PURCHASED,
        ])->count();
        $transactionsTotal = Transaction::count();
        $reportsPending = MonthlyReport::whereIn('status', [
            MonthlyReport::STATUS_DRAFT,
            MonthlyReport::STATUS_UNDER_REVIEW,
            MonthlyReport::STATUS_APPROVED,
        ])->count();

        $sidebarFinanceBadge = $paymentsPending + $purchasesActive + $reportsPending;
        $dashboardAttention = $ordersActive + $leadsNew + $projectsActive + $paymentsPending + $purchasesActive;

        return [
            'orders_active' => $ordersActive,
            'orders_waiting_payment' => $ordersWaitingPayment,
            'quotations_active' => $quotationsActive,
            'sales_orders_total_active' => $salesOrdersTotalActive,
            'leads_total_active' => $leadsActive,
            'leads_new' => $leadsNew,
            'customers_total' => $customersTotal,
            'services_total' => $servicesTotal,
            'sidebar_sales_badge' => $sidebarSalesBadge,
            'projects_active' => $projectsActive,
            'projects_new' => $projectsNew,
            'my_tasks_pending' => $myTasksPending,
            'qc_review_pending' => $qcReviewPending,
            'documentations_total' => $documentationsTotal,
            'sidebar_operations_badge' => $sidebarOperationsBadge,
            'payments_pending' => $paymentsPending,
            'purchases_active' => $purchasesActive,
            'transactions_total' => $transactionsTotal,
            'reports_pending' => $reportsPending,
            'sidebar_finance_badge' => $sidebarFinanceBadge,
            'dashboard_attention' => $dashboardAttention,
        ];
    }
}
