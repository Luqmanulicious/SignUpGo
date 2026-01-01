<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MailboxController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch all notifications for the user from database
        // This will include system notifications and EO notifications
        $notifications = DB::table('user_notifications')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // PostgreSQL requires explicit boolean comparison
        $unreadCount = DB::table('user_notifications')
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return view('mailbox.index', compact('notifications', 'unreadCount'));
    }
    
    public function markAsRead($notificationId)
    {
        DB::table('user_notifications')
            ->where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->update([
                'is_read' => true, 
                'read_at' => now()
            ]);
        
        return response()->json(['success' => true]);
    }
    
    public function destroy($notificationId)
    {
        DB::table('user_notifications')
            ->where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->delete();
        
        return redirect()->route('mailbox.index')
            ->with('success', 'Notification deleted successfully.');
    }
}
