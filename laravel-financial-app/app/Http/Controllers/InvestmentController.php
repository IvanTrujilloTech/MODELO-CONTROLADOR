<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function create()
    {
        return view('comprar_acciones');
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'empresa' => 'required|string|max:100',
            'cantidad' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0.01|max:999999.99',
        ]);
        
        $user = Auth::user();
        
        $total = $validatedData['cantidad'] * $validatedData['precio_compra'];
        $balance = Transaction::getBalance($user->id);
        
        if ($balance < $total) {
            return redirect()->back()->with('error', 'Saldo insuficiente para realizar la compra');
        }
        
        $investment = Investment::create([
            'user_id' => $user->id,
            'company' => $validatedData['empresa'],
            'quantity' => $validatedData['cantidad'],
            'purchase_price' => $validatedData['precio_compra'],
            'purchase_date' => now(),
        ]);
        
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'gasto',
            'category' => 'inversiones',
            'amount' => $total,
            'description' => 'Compra de acciones de ' . $validatedData['empresa'],
            'date' => now()->format('Y-m-d'),
        ]);
        
        return redirect()->route('investments')->with('success', 'Acciones compradas correctamente');
    }
    
    public function sell(Request $request)
    {
        $validatedData = $request->validate([
            'inversion_id' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
            'precio_venta' => 'required|numeric|min:0.01|max:999999.99',
        ]);
        
        $user = Auth::user();
        $investment = Investment::where('id', $validatedData['inversion_id'])
            ->where('user_id', $user->id)
            ->first();
            
        if (!$investment) {
            return redirect()->back()->with('error', 'Inversión no encontrada');
        }
        
        if ($validatedData['cantidad'] > $investment->quantity) {
            return redirect()->back()->with('error', 'Cantidad a vender excede la cantidad disponible');
        }
        
        $total = $validatedData['cantidad'] * $validatedData['precio_venta'];
        
        $investment->sell($validatedData['cantidad']);
        
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'ingreso',
            'category' => 'inversiones',
            'amount' => $total,
            'description' => 'Venta de acciones de ' . $investment->company,
            'date' => now()->format('Y-m-d'),
        ]);
        
        return redirect()->route('investments')->with('success', 'Acciones vendidas correctamente');
    }
}
