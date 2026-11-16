<?php

namespace App\Http\Controllers;

use App\Services\Workflow\StatusOverrideService;
use Illuminate\Http\Request;

class StatusOverrideController extends Controller
{
    public function override(Request $request, StatusOverrideService $service)
    {
        abort_if(! auth()->user()->isSuperAdmin(), 403, 'Akses ditolak: Hanya Super Admin yang berwenang melakukan override status.');

        $validated = $request->validate([
            'module' => 'required|string',
            'record_id' => 'required',
            'new_status' => 'required|string',
            'reason' => 'required|string|min:5',
        ]);

        try {
            $service->override(
                $validated['module'],
                $validated['record_id'],
                $validated['new_status'],
                $validated['reason']
            );

            return back()->with('success', 'Status '.$validated['module'].' #'.$validated['record_id'].' berhasil di-override menjadi '.$validated['new_status'].' oleh Super Admin.');
        } catch (\Exception $e) {
            return back()->withErrors(['override_error' => $e->getMessage()]);
        }
    }
}
