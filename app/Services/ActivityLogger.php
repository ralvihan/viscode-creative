<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

class ActivityLogger
{
    /**
     * Log an activity and broadcast in-app system notifications to authorized team members.
     */
    public static function log(
        string $module,
        string $recordId,
        string $activity,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $reason = null,
        ?int $userId = null
    ): ActivityLog {
        $actorId = $userId ?? Auth::id();

        $log = ActivityLog::create([
            'user_id' => $actorId,
            'module' => $module,
            'record_id' => $recordId,
            'activity' => $activity,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $reason,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);

        self::dispatchSystemNotifications($module, $recordId, $activity, $reason, $actorId);

        return $log;
    }

    /**
     * Create per-user SystemNotification entries based on logged activity.
     */
    public static function dispatchSystemNotifications(
        string $module,
        string $recordId,
        string $activity,
        ?string $reason,
        ?int $actorId = null
    ): void {
        if (! Schema::hasTable('system_notifications')) {
            return;
        }

        $meta = self::resolveNotificationMeta($module, $recordId, $activity, $reason, $actorId);
        $recipients = User::all()->filter(function (User $user) use ($meta): bool {
            return $user->canAccessModule($meta['permission_module']);
        });

        if ($recipients->isEmpty()) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($recipients as $recipient) {
            $rows[] = [
                'user_id' => $recipient->id,
                'actor_id' => $actorId,
                'category' => $meta['category'],
                'module' => $module,
                'record_id' => $recordId,
                'title' => $meta['title'],
                'message' => $meta['message'],
                'action_url' => $meta['action_url'],
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        SystemNotification::insert($rows);
    }

    /**
     * Resolve category, title, message, target URL, and permission module.
     *
     * @return array{category: string, title: string, message: string, action_url: string, permission_module: string}
     */
    public static function resolveNotificationMeta(
        string $module,
        string $recordId,
        string $activity,
        ?string $reason,
        ?int $actorId = null
    ): array {
        $modLower = strtolower(trim($module));
        $actUpper = strtoupper(trim($activity));
        $actorName = $actorId ? (User::whereKey($actorId)->value('name') ?? 'Tim Viscode') : 'Website Publik / Sistem';

        // Determine target route & RBAC module key
        $routeInfo = match (true) {
            str_contains($modLower, 'lead') => ['url' => route('leads.index'), 'perm' => 'leads'],
            str_contains($modLower, 'quotation') => ['url' => route('orders.index', ['filter' => 'waiting_response']), 'perm' => 'quotations'],
            str_contains($modLower, 'order') => ['url' => route('orders.index'), 'perm' => 'orders'],
            str_contains($modLower, 'payment') => ['url' => route('payments.index'), 'perm' => 'payments'],
            str_contains($modLower, 'purchase') => ['url' => route('purchases.index'), 'perm' => 'purchases'],
            str_contains($modLower, 'transaction') || str_contains($modLower, 'financial') => ['url' => route('transactions.index'), 'perm' => 'financials'],
            str_contains($modLower, 'report') => ['url' => route('reports.index'), 'perm' => 'reports'],
            str_contains($modLower, 'project') => ['url' => route('projects.index'), 'perm' => 'projects'],
            str_contains($modLower, 'task') => ['url' => route('projects.index', ['tab' => 'my_tasks']), 'perm' => 'tasks'],
            str_contains($modLower, 'doc') => ['url' => route('documentations.index'), 'perm' => 'documentation'],
            str_contains($modLower, 'masteruser') => ['url' => route('master.index', ['tab' => 'users']), 'perm' => 'master_data'],
            str_contains($modLower, 'masterservice') => ['url' => route('master.index', ['tab' => 'services']), 'perm' => 'master_data'],
            str_contains($modLower, 'mastercustomer') => ['url' => route('master.index', ['tab' => 'customers']), 'perm' => 'master_data'],
            str_contains($modLower, 'customer') => ['url' => route('customers.index'), 'perm' => 'customers'],
            default => ['url' => route('dashboard'), 'perm' => 'activity_logs'],
        };

        // Determine notification category & Indonesian title
        if (
            (str_contains($modLower, 'order') || str_contains($modLower, 'lead') || str_contains($modLower, 'quotation'))
            && (str_contains($actUpper, 'CREATE') || str_contains($actUpper, 'PUBLIC') || str_contains($actUpper, 'CONVERT'))
        ) {
            $category = SystemNotification::CATEGORY_PESANAN_BARU;
            $title = match (true) {
                str_contains($modLower, 'order') => 'Pesanan Baru Masuk',
                str_contains($modLower, 'lead') => 'Prospek / Lead Baru Masuk',
                default => 'Penawaran Harga Baru Dibuat',
            };
        } elseif (
            str_contains($modLower, 'payment')
            || str_contains($modLower, 'purchase')
            || str_contains($modLower, 'transaction')
            || str_contains($modLower, 'report')
        ) {
            $category = SystemNotification::CATEGORY_KEUANGAN;
            $title = match (true) {
                str_contains($modLower, 'payment') => 'Aktivitas Pembayaran Klien',
                str_contains($modLower, 'purchase') => 'Pengajuan & Pembelian Operasional',
                str_contains($modLower, 'report') => 'Pembaruan Laporan Bulanan',
                default => 'Transaksi Buku Kas Dicatat',
            };
        } elseif (
            str_contains($modLower, 'project')
            || str_contains($modLower, 'task')
            || str_contains($modLower, 'doc')
        ) {
            $category = SystemNotification::CATEGORY_OPERASIONAL;
            $title = match (true) {
                str_contains($modLower, 'task') => 'Update Tugas Proyek',
                str_contains($modLower, 'doc') => 'Deliverable & Aset Proyek',
                default => 'Progres Proyek & QC',
            };
        } elseif (
            str_contains($actUpper, 'UPDATE')
            || str_contains($actUpper, 'DELETE')
            || str_contains($actUpper, 'TOGGLE')
            || str_contains($actUpper, 'OVERRIDE')
            || str_contains($modLower, 'master')
            || str_contains($modLower, 'customer')
        ) {
            $category = SystemNotification::CATEGORY_PERUBAHAN_DATA;
            $title = str_contains($modLower, 'master')
                ? 'Perubahan Data Master'
                : (str_contains($actUpper, 'OVERRIDE') ? 'Override Status Sistem' : 'Perubahan Data '.$module);
        } else {
            $category = SystemNotification::CATEGORY_USER_ACTION;
            $title = 'Aksi User pada '.$module;
        }

        $detailText = $reason ?: ($activity.' pada '.$module.' (#'.$recordId.')');
        $message = $actorName.': '.$detailText;

        return [
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'action_url' => $routeInfo['url'],
            'permission_module' => $routeInfo['perm'],
        ];
    }
}
