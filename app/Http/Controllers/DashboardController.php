<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
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
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $user = Auth::user();

        // ---------------------------------------------------------
        // SECTION A: BUSINESS OVERVIEW (Real Database Values)
        // ---------------------------------------------------------
        $monthlyIncome = (float) Transaction::where('type', 'income')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $monthlyExpense = (float) Transaction::where('type', 'expense')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $netProfit = $monthlyIncome - $monthlyExpense;

        $activeOrdersCount = Order::whereNotIn('status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])->count();
        $completedOrdersCount = Order::where('status', Order::STATUS_COMPLETED)->count();
        $totalCustomers = Customer::count();
        $activeProjectsCount = Project::whereNotIn('status', [Project::STATUS_COMPLETED, Project::STATUS_CANCELLED])->count();

        // Total Piutang (Outstanding Receivables from active orders)
        $unpaidOrders = Order::with('payments')
            ->whereNotIn('status', [Order::STATUS_CANCELLED])
            ->whereIn('payment_status', [Order::PAYMENT_UNPAID, Order::PAYMENT_PARTIALLY_PAID])
            ->get();
        $outstandingReceivables = (float) $unpaidOrders->sum(fn ($ord) => $ord->remainingBalance());

        // ---------------------------------------------------------
        // SECTION B: ACTION CENTER (Role-Based Action Priorities)
        // ---------------------------------------------------------
        $actionCards = [];

        // 1. Sales & Customer Relations Actions
        if ($user->isSuperAdmin() || $user->isSales() || $user->isCeo()) {
            $activeLeads = Lead::with('service')
                ->whereIn('status', [Lead::STATUS_NEW, Lead::STATUS_REVIEWED, Lead::STATUS_FOLLOW_UP])
                ->latest()
                ->take(5)
                ->get();
            foreach ($activeLeads as $l) {
                $actionCards[] = [
                    'category' => 'Sales Pipeline',
                    'title' => match ($l->status) {
                        Lead::STATUS_NEW => 'Review Lead Baru Masuk',
                        Lead::STATUS_REVIEWED => 'Catat Follow-up Komunikasi',
                        default => 'Terbitkan Surat Penawaran (Quotation)',
                    },
                    'record_type' => 'Lead',
                    'record_code' => $l->lead_code,
                    'customer_name' => $l->customer_name,
                    'meta' => ($l->service?->name ?? 'Konsultasi Layanan').' — '.match ($l->status) {
                        Lead::STATUS_NEW => 'Tinjau data prospek dari '.$l->source,
                        Lead::STATUS_REVIEWED => 'Hubungi prospek dan catat hasil komunikasi',
                        default => 'Prospek siap diberikan penawaran harga',
                    },
                    'amount' => $l->estimated_value,
                    'deadline' => $l->next_follow_up_date,
                    'status' => $l->status,
                    'status_color' => match ($l->status) {
                        Lead::STATUS_NEW => 'bg-blue-50 text-blue-700 border-blue-200',
                        Lead::STATUS_REVIEWED => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                    },
                    'role_label' => 'Sales & Customer Relations',
                    'action_label' => match ($l->status) {
                        Lead::STATUS_NEW => 'Review Lead',
                        Lead::STATUS_REVIEWED => 'Catat Follow-up',
                        default => 'Buat Penawaran',
                    },
                    'action_url' => route('leads.index').'?search='.$l->lead_code,
                    'badge' => 'LEAD '.$l->status,
                ];
            }

            $sentQuotations = Quotation::with('customer', 'service')
                ->whereIn('status', [Quotation::STATUS_DRAFT, Quotation::STATUS_SENT, Quotation::STATUS_ACCEPTED])
                ->latest()
                ->take(5)
                ->get();
            foreach ($sentQuotations as $q) {
                $actionCards[] = [
                    'category' => 'Sales & Orders',
                    'title' => match ($q->status) {
                        Quotation::STATUS_DRAFT => 'Kirim Draft Penawaran ke Klien',
                        Quotation::STATUS_ACCEPTED => 'Konversi Penawaran Disetujui ke Order',
                        default => 'Menunggu Respon Klien (Quotation)',
                    },
                    'record_type' => 'Quotation',
                    'record_code' => $q->quotation_code,
                    'customer_name' => $q->customer->name ?? '-',
                    'meta' => ($q->service->name ?? '-').' — '.match ($q->status) {
                        Quotation::STATUS_DRAFT => 'Penawaran masih berstatus DRAFT',
                        Quotation::STATUS_ACCEPTED => 'Klien telah setuju, siap konversi otomatis ke Order',
                        default => 'Catat persetujuan atau penolakan klien',
                    },
                    'amount' => $q->total_amount,
                    'deadline' => $q->valid_until,
                    'status' => $q->status,
                    'status_color' => $q->status === Quotation::STATUS_ACCEPTED ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200',
                    'role_label' => 'Sales & Customer Relations',
                    'action_label' => $q->status === Quotation::STATUS_ACCEPTED ? 'Konversi ke Order' : 'Buka Penawaran',
                    'action_url' => route('orders.index', ['filter' => 'waiting_response']),
                    'badge' => 'QUOTATION '.$q->status,
                ];
            }
        }

        // 2. Finance Manager Actions
        if ($user->isSuperAdmin() || $user->isFinance() || $user->isCeo()) {
            $pendingPayments = Payment::with('order.customer')
                ->where('status', Payment::STATUS_PENDING)
                ->latest()
                ->take(5)
                ->get();
            foreach ($pendingPayments as $p) {
                $actionCards[] = [
                    'category' => 'Finance',
                    'title' => 'Verifikasi Pembayaran Masuk',
                    'record_type' => 'Payment',
                    'record_code' => $p->payment_code,
                    'customer_name' => $p->order->customer->name,
                    'meta' => 'Pesanan: '.$p->order->order_code,
                    'amount' => $p->amount,
                    'deadline' => null,
                    'status' => $p->status,
                    'status_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'role_label' => 'Finance Manager',
                    'action_label' => 'Verifikasi Pembayaran',
                    'action_url' => route('payments.index'),
                    'badge' => 'VERIFIKASI BAYAR',
                ];
            }

            $purchasedAwaitingFinalize = PurchaseRequest::where('status', PurchaseRequest::STATUS_PURCHASED)
                ->latest()
                ->take(4)
                ->get();
            foreach ($purchasedAwaitingFinalize as $pr) {
                $actionCards[] = [
                    'category' => 'Finance',
                    'title' => 'Bukukan Nota Belanja ke Kas',
                    'record_type' => 'Purchase',
                    'record_code' => $pr->request_code,
                    'customer_name' => $pr->title,
                    'meta' => 'Diajukan oleh: '.($pr->requester->name ?? 'Tim'),
                    'amount' => $pr->actual_amount ?? $pr->estimated_amount,
                    'deadline' => null,
                    'status' => $pr->status,
                    'status_color' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'role_label' => 'Finance Manager',
                    'action_label' => 'Bukukan Kas Usaha',
                    'action_url' => route('purchases.index'),
                    'badge' => 'BUKUKAN NOTA',
                ];
            }
        }

        // 3. Project Manager & CEO Actions
        if ($user->isSuperAdmin() || $user->isCeo()) {
            // Orders confirmed needing project creation
            $confirmedOrdersNoProject = Order::with('customer', 'service')
                ->where('status', Order::STATUS_CONFIRMED)
                ->doesntHave('project')
                ->latest()
                ->take(5)
                ->get();
            foreach ($confirmedOrdersNoProject as $co) {
                $actionCards[] = [
                    'category' => 'Project Management',
                    'title' => 'Inisialisasi Proyek dari Pesanan',
                    'record_type' => 'Order',
                    'record_code' => $co->order_code,
                    'customer_name' => $co->customer->name,
                    'meta' => $co->service->name.' (DP Terpenuhi)',
                    'amount' => $co->agreed_price,
                    'deadline' => $co->deadline,
                    'status' => $co->status,
                    'status_color' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'role_label' => 'Project Manager',
                    'action_label' => 'Inisialisasi Proyek',
                    'action_url' => route('projects.index'),
                    'badge' => 'BUAT PROYEK',
                ];
            }

            // Purchases awaiting CEO approval
            $pendingPurchases = PurchaseRequest::with('requester')
                ->where('status', PurchaseRequest::STATUS_PENDING)
                ->latest()
                ->take(5)
                ->get();
            foreach ($pendingPurchases as $pr) {
                $actionCards[] = [
                    'category' => 'Executive',
                    'title' => 'Persetujuan Pengajuan Belanja (CEO)',
                    'record_type' => 'Purchase',
                    'record_code' => $pr->request_code,
                    'customer_name' => $pr->title,
                    'meta' => 'Pemohon: '.($pr->requester->name ?? 'Tim').' - '.$pr->purpose,
                    'amount' => $pr->estimated_amount,
                    'deadline' => null,
                    'status' => $pr->status,
                    'status_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'role_label' => 'CEO / Super Admin',
                    'action_label' => 'Setujui Pembelian',
                    'action_url' => route('purchases.index'),
                    'badge' => 'PERSETUJUAN CEO',
                ];
            }

            // Reports awaiting CEO review
            $underReviewReports = MonthlyReport::where('status', MonthlyReport::STATUS_UNDER_REVIEW)
                ->latest()
                ->take(3)
                ->get();
            foreach ($underReviewReports as $rep) {
                $actionCards[] = [
                    'category' => 'Executive',
                    'title' => 'Tinjau & Setujui Laporan Bulanan (CEO)',
                    'record_type' => 'Report',
                    'record_code' => $rep->report_code,
                    'customer_name' => 'Laporan Periode '.$rep->month.'/'.$rep->year,
                    'meta' => 'Laba Bersih: Rp '.number_format($rep->net_profit, 0, ',', '.'),
                    'amount' => $rep->net_profit,
                    'deadline' => null,
                    'status' => $rep->status,
                    'status_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'role_label' => 'CEO / Super Admin',
                    'action_label' => 'Tinjau Laporan',
                    'action_url' => route('reports.index', ['month' => $rep->month, 'year' => $rep->year]),
                    'badge' => 'REVIEW LAPORAN',
                ];
            }
        }

        // 4. Quality Control Actions
        if ($user->isSuperAdmin() || $user->isQc() || $user->isCeo()) {
            $qcProjects = Project::with('order.customer', 'tasks')
                ->where('status', Project::STATUS_REVIEW)
                ->latest()
                ->take(5)
                ->get();
            foreach ($qcProjects as $qp) {
                $actionCards[] = [
                    'category' => 'Quality Control',
                    'title' => 'Pemeriksaan Hasil Proyek (QC)',
                    'record_type' => 'Project',
                    'record_code' => $qp->project_code,
                    'customer_name' => $qp->order->customer->name,
                    'meta' => $qp->title.' (Deliverables Siap Uji)',
                    'amount' => null,
                    'deadline' => $qp->target_completion_date,
                    'status' => $qp->status,
                    'status_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'role_label' => 'Quality Control & Admin',
                    'action_label' => 'Periksa QC',
                    'action_url' => route('projects.index'),
                    'badge' => 'IN REVIEW QC',
                ];
            }
        }

        // 5. Developer / Designer Actions (My Active Tasks & Revisions)
        if ($user->isTechnicalTeam() || $user->isSuperAdmin()) {
            $myTasks = Task::with('project.order.customer')
                ->where('assigned_to', $user->id)
                ->whereIn('status', [Task::STATUS_TODO, Task::STATUS_IN_PROGRESS, Task::STATUS_REVISION])
                ->latest()
                ->take(5)
                ->get();
            foreach ($myTasks as $tk) {
                $actionCards[] = [
                    'category' => 'Production',
                    'title' => $tk->status === Task::STATUS_REVISION ? 'Revisi Tugas Pengerjaan' : 'Tugas Sedang Aktif',
                    'record_type' => 'Task',
                    'record_code' => 'TASK-'.$tk->id,
                    'customer_name' => $tk->title,
                    'meta' => 'Proyek: '.($tk->project->title ?? '-'),
                    'amount' => null,
                    'deadline' => $tk->deadline,
                    'status' => $tk->status,
                    'status_color' => $tk->status === Task::STATUS_REVISION ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200',
                    'role_label' => 'Technical Member',
                    'action_label' => 'Buka Tugas',
                    'action_url' => route('projects.index'),
                    'badge' => $tk->status === Task::STATUS_REVISION ? 'PERLU REVISI' : 'TUGAS AKTIF',
                ];
            }
        }

        // ---------------------------------------------------------
        // SECTION C: BUSINESS WORKFLOW TRACKER (5 Stages Overview)
        // ---------------------------------------------------------
        $stage1_sales = Order::with(['customer', 'service'])
            ->where('status', Order::STATUS_PENDING_PAYMENT)
            ->where('payment_status', Order::PAYMENT_UNPAID)
            ->latest()
            ->get();

        $stage2_payment = Order::with(['customer', 'service', 'payments'])
            ->where('payment_status', Order::PAYMENT_PARTIALLY_PAID)
            ->orWhere(function ($q) {
                $q->where('status', Order::STATUS_PENDING_PAYMENT)
                    ->whereHas('payments', function ($p) {
                        $p->where('status', Payment::STATUS_PENDING);
                    });
            })
            ->latest()
            ->get();

        $stage3_in_progress = Order::with(['customer', 'service', 'project'])
            ->whereIn('status', [Order::STATUS_CONFIRMED, Order::STATUS_IN_PROGRESS])
            ->whereHas('project', function ($p) {
                $p->whereIn('status', [Project::STATUS_NOT_STARTED, Project::STATUS_IN_PROGRESS]);
            })
            ->latest()
            ->get();

        $stage4_qc = Order::with(['customer', 'service', 'project'])
            ->whereHas('project', function ($p) {
                $p->whereIn('status', [Project::STATUS_REVIEW, Project::STATUS_REVISION]);
            })
            ->latest()
            ->get();

        $stage5_completed = Order::with(['customer', 'service', 'project'])
            ->where('status', Order::STATUS_COMPLETED)
            ->latest()
            ->take(10)
            ->get();

        $workflowTracker = [
            'sales' => [
                'name' => '1. Sales & Penawaran',
                'description' => 'Pesanan baru masuk & penawaran belum dibayar DP',
                'count' => $stage1_sales->count(),
                'orders' => $stage1_sales,
            ],
            'payment' => [
                'name' => '2. Menunggu Verifikasi Pembayaran',
                'description' => 'Pesanan menunggu verifikasi bukti transfer DP / pelunasan',
                'count' => $stage2_payment->count(),
                'orders' => $stage2_payment,
            ],
            'in_progress' => [
                'name' => '3. Pengerjaan Teknis (In Progress)',
                'description' => 'Proyek sedang dikerjakan developer & desainer',
                'count' => $stage3_in_progress->count(),
                'orders' => $stage3_in_progress,
            ],
            'qc' => [
                'name' => '4. Review & Quality Control',
                'description' => 'Deliverable sedang ditinjau QC atau dalam masa perbaikan',
                'count' => $stage4_qc->count(),
                'orders' => $stage4_qc,
            ],
            'completed' => [
                'name' => '5. Selesai & Terverifikasi',
                'description' => 'Deliverables terkirim dan pembayaran beres 100%',
                'count' => $stage5_completed->count(),
                'orders' => $stage5_completed,
            ],
        ];

        // ---------------------------------------------------------
        // SECTION D: RECENT ACTIVITY (System Events Audit)
        // ---------------------------------------------------------
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        // Data List & References
        $recentOrders = Order::with(['customer', 'service', 'pic'])
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = Transaction::with('recorder')
            ->latest('transaction_date')
            ->take(5)
            ->get();

        $recentProjects = Project::with(['order.customer', 'projectManager'])
            ->latest()
            ->take(5)
            ->get();

        $services = Service::where('is_active', true)->get();
        $team = User::where('role', '!=', 'customer')->get();
        $activeOrdersList = Order::with('customer')
            ->whereNotIn('status', [Order::STATUS_CANCELLED])
            ->latest()
            ->get();
        $orders = $activeOrdersList;

        return view('dashboard', compact(
            'monthlyIncome', 'monthlyExpense', 'netProfit',
            'activeOrdersCount', 'completedOrdersCount', 'totalCustomers',
            'activeProjectsCount', 'outstandingReceivables',
            'actionCards', 'workflowTracker', 'recentActivities',
            'recentOrders', 'recentTransactions', 'recentProjects',
            'services', 'team', 'activeOrdersList', 'orders'
        ));
    }
}
