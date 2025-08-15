<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead()
    {
        // Tandai semua notifikasi milik user yang belum dibaca menjadi sudah dibaca
        Auth::user()->customNotifications()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['status' => 'success']);
    }
}