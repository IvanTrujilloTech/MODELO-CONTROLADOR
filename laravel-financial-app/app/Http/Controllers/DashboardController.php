<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        $balance = Transaction::getBalance($user->id);
        $transactions = $user->transactions()->orderBy('date', 'desc')->get();
        
        $monthly_income = 0;
        $monthly_expenses = 0;
        $current_month = now()->format('Y-m');
        
        foreach ($transactions as $t) {
            $transaction_month = $t->date->format('Y-m');
            if ($transaction_month == $current_month) {
                if ($t->type == 'ingreso') {
                    $monthly_income += (float)$t->amount;
                } elseif ($t->type == 'gasto') {
                    $monthly_expenses += (float)$t->amount;
                }
            }
        }
        
        return view('dashboard', [
            'balance' => $balance,
            'transactions' => $transactions,
            'monthly_income' => $monthly_income,
            'monthly_expenses' => $monthly_expenses
        ]);
    }
    
    public function investments()
    {
        $user = Auth::user();
        $investments = $user->investments()->orderBy('purchase_date', 'desc')->get();
        
        return view('investments', [
            'investments' => $investments
        ]);
    }
    
    public function reset()
    {
        $user = Auth::user();
        
        // Delete transactions
        $user->transactions()->delete();
        
        // Delete investments
        $user->investments()->delete();
        
        return redirect()->route('dashboard')->with('success', 'Datos de prueba reseteados correctamente');
    }
}
