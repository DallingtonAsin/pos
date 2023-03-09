<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{


    public function __construct()
    {
    }

    public function GetUnReadEmailNotifications()
    {
        //$UnreadEmailNotifications = Auth::user()->unreadNotifications;
        $UnreadEmailNotifications  = Notification::where('notifiable_id', Auth::user()->id)
            ->where('read_at', null)
            ->where('type', "App\Notifications\NewEmailNotifier")
            ->get();
        return $UnreadEmailNotifications;
    }

    public function GetOtherNotifications()
    {
        $UnreadNotifications = Auth::user()->unreadNotifications;
        return $UnreadNotifications;
    }


    public function markAllRead()
    {
        $user = Auth::user();
        foreach ($user->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return back();
    }
}
