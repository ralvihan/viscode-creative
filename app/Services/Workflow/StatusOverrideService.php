<?php

namespace App\Services\Workflow;

use App\Models\Lead;
use App\Models\MonthlyReport;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\Quotation;
use App\Models\Task;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StatusOverrideService
{
    /**
     * Map of modules to their allowed statuses and model classes
     */
    public const MODULE_CONFIG = [
        'leads' => [
            'model' => Lead::class,
            'code_field' => 'lead_code',
            'statuses' => ['NEW', 'REVIEWED', 'FOLLOW_UP', 'QUOTATION_SENT', 'CONTACTED', 'OFFERED', 'WON', 'LOST'],
        ],
        'quotations' => [
            'model' => Quotation::class,
            'code_field' => 'quotation_code',
            'statuses' => ['DRAFT', 'SENT', 'ACCEPTED', 'REJECTED', 'EXPIRED', 'CONVERTED'],
        ],
        'orders' => [
            'model' => Order::class,
            'code_field' => 'order_code',
            'statuses' => ['PENDING_PAYMENT', 'CONFIRMED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'],
        ],
        'payments' => [
            'model' => Payment::class,
            'code_field' => 'payment_code',
            'statuses' => ['PENDING', 'VERIFIED', 'REJECTED', 'VOIDED'],
        ],
        'projects' => [
            'model' => Project::class,
            'code_field' => 'project_code',
            'statuses' => ['NOT_STARTED', 'IN_PROGRESS', 'REVIEW', 'REVISION', 'COMPLETED', 'CANCELLED'],
        ],
        'tasks' => [
            'model' => Task::class,
            'code_field' => 'id',
            'statuses' => ['TODO', 'IN_PROGRESS', 'IN_REVIEW', 'REVISION', 'DONE'],
        ],
        'purchases' => [
            'model' => PurchaseRequest::class,
            'code_field' => 'request_code',
            'statuses' => ['PENDING', 'APPROVED', 'REJECTED', 'PURCHASED', 'COMPLETED', 'CANCELLED'],
        ],
        'reports' => [
            'model' => MonthlyReport::class,
            'code_field' => 'report_code',
            'statuses' => ['DRAFT', 'UNDER_REVIEW', 'APPROVED', 'FINALIZED'],
        ],
    ];

    public function override(string $module, int|string $recordId, string $newStatus, string $reason, ?User $user = null): mixed
    {
        $user = $user ?? Auth::user();

        if (! $user || ! $user->isSuperAdmin()) {
            throw new InvalidArgumentException('Hanya Super Admin yang berwenang melakukan override status.');
        }

        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan override status wajib diisi secara lengkap.');
        }

        $moduleKey = strtolower($module);
        if (! isset(self::MODULE_CONFIG[$moduleKey])) {
            throw new InvalidArgumentException('Modul '.$module.' tidak mendukung override status.');
        }

        $config = self::MODULE_CONFIG[$moduleKey];
        if (! in_array($newStatus, $config['statuses'], true)) {
            throw new InvalidArgumentException('Status tujuan '.$newStatus.' tidak valid untuk modul '.$module.'.');
        }

        $modelClass = $config['model'];
        $record = $modelClass::findOrFail($recordId);

        $oldStatus = $record->status;
        if ($oldStatus === $newStatus) {
            throw new InvalidArgumentException('Status baru sama dengan status saat ini.');
        }

        return DB::transaction(function () use ($record, $config, $moduleKey, $oldStatus, $newStatus, $reason, $user) {
            $codeField = $config['code_field'];
            $codeIdentifier = $record->$codeField ?? (string) $record->id;

            $oldData = ['status' => $oldStatus];
            $newData = ['status' => $newStatus];

            $record->status = $newStatus;
            $record->save();

            // When order is overridden, ensure payment status sync if necessary
            if ($record instanceof Order && $newStatus === Order::STATUS_CONFIRMED && $record->payment_status === Order::PAYMENT_UNPAID) {
                // Keep integrity but note
            }

            // Log Super Admin Action
            ActivityLogger::log(
                ucfirst($moduleKey),
                (string) $codeIdentifier,
                'SUPER_ADMIN_STATUS_OVERRIDE',
                $oldData,
                $newData,
                $reason,
                $user->id
            );

            return $record;
        });
    }
}
