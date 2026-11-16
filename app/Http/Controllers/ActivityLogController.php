<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        abort_if(! auth()->user()->isSuperAdmin() && ! auth()->user()->isCeo(), 403, 'Hanya Super Admin dan CEO yang memiliki akses ke Audit Trail / Activity Log.');

        $query = ActivityLog::with('user')->latest('id');

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('record_id', 'like', "%{$search}%")
                    ->orWhere('activity', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        $modules = ActivityLog::select('module')->distinct()->pluck('module');

        return view('activity_logs.index', compact('logs', 'modules'));
    }
}
