<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function create()
    {
        return view('add_transaction');
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:ingreso,gasto',
            'category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'description' => 'required|string|max:200',
            'date' => 'required|date',
        ]);
        
        $user = Auth::user();
        
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => $validatedData['type'],
            'category' => $validatedData['category'],
            'amount' => $validatedData['amount'],
            'description' => $validatedData['description'],
            'date' => $validatedData['date'],
        ]);
        
        // Send webhook notification
        $this->sendWebhookNotification($transaction);
        
        return redirect()->route('dashboard')->with('success', 'Transacción añadida correctamente');
    }
    
    private function sendWebhookNotification($transaction)
    {
        $user = $transaction->user;
        
        $data = [
            'tipo' => 'nueva_transaccion',
            'usuario_id' => $user->id,
            'usuario_nombre' => $user->name,
            'transaccion' => [
                'tipo' => $transaction->type,
                'categoria' => $transaction->category,
                'monto' => $transaction->amount,
                'descripcion' => $transaction->description,
                'fecha' => $transaction->date->format('Y-m-d'),
            ],
        ];
        
        $webhookUrl = config('app.n8n_webhook_url') ?: 'https://ivantrubar.app.n8n.cloud/webhook-test/ff373657-1ce7-4512-9329-1b534d87c759';
        
        if ($webhookUrl) {
            $payload = [
                'event' => 'nueva_transaccion',
                'timestamp' => now()->toISOString(),
                'data' => $data,
                'metadata' => [
                    'source' => 'laravel-financial-app',
                    'version' => '1.0',
                ],
            ];
            
            try {
                $client = new \GuzzleHttp\Client();
                $client->post($webhookUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Webhook-Source' => 'laravel-financial-app',
                    ],
                    'json' => $payload,
                    'timeout' => 30,
                ]);
            } catch (\Exception $e) {
                error_log('Webhook error: ' . $e->getMessage());
            }
        }
    }
}
