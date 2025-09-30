<?php

namespace App\Http\Controllers;

use App\Models\CamsNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notifications page
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = CamsNotification::forUser($user->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->read();
            } elseif ($request->status === 'unread') {
                $query->unread();
            }
        }

        $notifications = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => CamsNotification::forUser($user->id)->count(),
            'unread' => CamsNotification::forUser($user->id)->unread()->count(),
            'critical' => CamsNotification::forUser($user->id)->byPriority('critical')->unread()->count(),
        ];

        $viewData = [
            'meta_title' => 'Notifications | CAMS',
            'meta_desc' => 'View and manage your CAMS notifications',
            'meta_image' => url('logo.png'),
            'notifications' => $notifications,
            'stats' => $stats,
            'filters' => $request->only(['type', 'priority', 'status']),
            'types' => CamsNotification::getTypes(),
        ];

        return view('social-worker.notifications.index', $viewData);
    }

    /**
     * Get notifications for dropdown (AJAX)
     */
    public function getNotifications(): JsonResponse
    {
        $user = Auth::user();

        $notifications = CamsNotification::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = NotificationService::getUnreadCount($user);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(CamsNotification $notification): JsonResponse
    {
        // Ensure user owns the notification
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        NotificationService::markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(CamsNotification $notification): JsonResponse
    {
        // Ensure user owns the notification
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = NotificationService::getUnreadCount($user);

        return response()->json([
            'unread_count' => $count,
        ]);
    }
}
