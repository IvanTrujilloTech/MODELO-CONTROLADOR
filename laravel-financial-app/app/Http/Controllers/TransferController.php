<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        $transfers = Transfer::getTransfersForUser($user->id);
        
        $users = User::where('id', '!=', $user->id)->get();
        
        return view('transfers', [
            'transfers' => $transfers,
            'users' => $users
        ]);
    }
    
    public function create()
    {
        $user = Auth::user();
        $users = User::where('id', '!=', $user->id)->get();
        
        return view('create_transfer', ['users' => $users]);
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'recipient_id' => 'required|integer|different:' . Auth::id(),
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'description' => 'nullable|string|max:200',
        ]);
        
        $user = Auth::user();
        $balance = Transaction::getBalance($user->id);
        
        if ($balance < $validatedData['amount']) {
            return redirect()->back()->with('error', 'Saldo insuficiente para realizar la transferencia');
        }
        
        $transfer = Transfer::create([
            'sender_id' => $user->id,
            'recipient_id' => $validatedData['recipient_id'],
            'amount' => $validatedData['amount'],
            'description' => $validatedData['description'],
            'timestamp' => now(),
        ]);
        
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'gasto',
            'category' => 'transferencias',
            'amount' => $validatedData['amount'],
            'description' => 'Transferencia a ' . $transfer->recipient->name,
            'date' => now()->format('Y-m-d'),
        ]);
        
        Transaction::create([
            'user_id' => $validatedData['recipient_id'],
            'type' => 'ingreso',
            'category' => 'transferencias',
            'amount' => $validatedData['amount'],
            'description' => 'Transferencia de ' . $user->name,
            'date' => now()->format('Y-m-d'),
        ]);
        
        return redirect()->route('transfers')->with('success', 'Transferencia realizada correctamente');
    }
}
