<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show the chat interface.
     */
    public function index()
    {
        return view('chat.index', [
            'user' => Auth::user(),
            'firebaseConfig' => config('firebase'),
        ]);
    }
}
