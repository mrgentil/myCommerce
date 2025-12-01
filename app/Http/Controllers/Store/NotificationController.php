<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.customer');
    }

    /**
     * Get customer notifications
     */
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        $notifications = UserNotification::where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('themes.xylo.customer.notifications.index', compact('notifications'));
    }

    /**
     * Get notifications as JSON (for dropdown)
     */
    public function getNotifications()
    {
        $customerId = Auth::guard('customer')->id();

        $notifications = UserNotification::where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'icon' => $n->icon,
                    'color' => $n->color,
                    'action_url' => $n->action_url,
                    'read' => $n->isRead(),
                    'time' => $n->created_at->diffForHumans(),
                ];
            }),
            'unread_count' => UserNotification::where('notifiable_type', 'App\Models\Customer')
                ->where('notifiable_id', $customerId)
                ->unread()
                ->count(),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $customerId = Auth::guard('customer')->id();

        $notification = UserNotification::where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customerId)
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        $customerId = Auth::guard('customer')->id();

        UserNotification::where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customerId)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $customerId = Auth::guard('customer')->id();

        $count = UserNotification::where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customerId)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Notification preferences
     */
    public function preferences()
    {
        $customerId = Auth::guard('customer')->id();
        $preferences = NotificationPreference::getForUser('App\Models\Customer', $customerId);

        return view('themes.xylo.customer.notifications.preferences', compact('preferences'));
    }

    /**
     * Update preferences
     */
    public function updatePreferences(Request $request)
    {
        $customerId = Auth::guard('customer')->id();
        $preferences = NotificationPreference::getForUser('App\Models\Customer', $customerId);

        $preferences->update([
            'email_orders' => $request->boolean('email_orders'),
            'email_messages' => $request->boolean('email_messages'),
            'email_reviews' => $request->boolean('email_reviews'),
            'email_promotions' => $request->boolean('email_promotions'),
            'email_newsletter' => $request->boolean('email_newsletter'),
            'push_enabled' => $request->boolean('push_enabled'),
        ]);

        return redirect()->back()->with('success', 'Préférences mises à jour');
    }
}
