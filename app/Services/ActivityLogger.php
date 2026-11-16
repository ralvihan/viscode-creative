<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity
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
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
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
    }
}
