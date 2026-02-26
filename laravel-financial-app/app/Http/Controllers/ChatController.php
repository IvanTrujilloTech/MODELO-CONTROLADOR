<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('chat');
    }
    
    public function send(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|integer',
            'message' => 'required|string|max:500',
        ]);
        
        $user = Auth::user();
        
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $validatedData['receiver_id'],
            'message' => $validatedData['message'],
            'timestamp' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
    
    public function messages($receiver_id)
    {
        $user = Auth::user();
        
        $messages = Message::getMessagesBetweenUsers($user->id, $receiver_id);
        
        return response()->json([
            'messages' => $messages,
        ]);
    }
}
