<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read (AJAX or standard redirect).
     */
    public function markRead(Request $request, SystemNotification $notification): JsonResponse|RedirectResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        $unreadCount = SystemNotification::where('user_id', auth()->id())->unread()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'unread_count' => $unreadCount,
                'read_at' => $notification->read_at?->toISOString(),
            ]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark notification as read and redirect directly to its target page.
     */
    public function visit(SystemNotification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        $targetUrl = $notification->action_url ?: route('dashboard');

        return redirect()->to($targetUrl);
    }

    /**
     * Delete a single notification and return updated unread count.
     */
    public function destroy(Request $request, SystemNotification $notification): JsonResponse|RedirectResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->delete();

        $unreadCount = SystemNotification::where('user_id', auth()->id())->unread()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'deleted',
                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        SystemNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Seluruh notifikasi telah ditandai dibaca.');
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function clearAll(Request $request): JsonResponse|RedirectResponse
    {
        SystemNotification::where('user_id', auth()->id())->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'cleared',
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Seluruh notifikasi berhasil dibersihkan.');
    }
}
