<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        if (Auth::id() !== $notification->user_id) {
            return response()->json(['success' => false, 'error' => 'No tienes permisos para marcar esta notificación como leída']);
        }

        $notification->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Notification $notification)
    {
        if (Auth::id() !== $notification->user_id) {
            return response()->json(['success' => false, 'error' => 'No tienes permisos para eliminar esta notificación']);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }
}
