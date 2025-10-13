<?php
// app/Http/Controllers/SalesController.php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index()
    {
        $sales_data = Sale::latest()->get();
        
        // Calculate today's total
        $total_today = Sale::whereDate('created_at', today())->sum('amount');
        
        return view('sales', compact('sales_data', 'total_today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'product' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Create sale
            Sale::create($validated);

            // Update inventory if product specified
            if ($request->product && $request->quantity) {
                $productName = strtolower($request->product);
                
                $inventory = Inventory::where('product', 'like', "%{$productName}%")->first();
                
                if ($inventory) {
                    if ($inventory->qty >= $request->quantity) {
                        $inventory->decrement('qty', $request->quantity);
                        
                        // Record inventory history
                        InventoryHistory::create([
                            'product' => $inventory->product,
                            'action' => 'OUT',
                            'quantity' => $request->quantity,
                            'remaining_stock' => $inventory->qty,
                            'notes' => "Sale to {$request->company}",
                        ]);
                    }
                }
            }
        });

        return redirect()->route('sales')
            ->with('success', 'Sale recorded successfully!');
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        return redirect()->route('sales')
            ->with('success', 'Sale deleted successfully!');
    }
}