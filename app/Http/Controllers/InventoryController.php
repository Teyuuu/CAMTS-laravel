<?php
// app/Http/Controllers/InventoryController.php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory_data = Inventory::all();
        $inventory_history = InventoryHistory::latest()->take(10)->get();

        return view('inventory', compact('inventory_data', 'inventory_history'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product' => 'required|string|unique:inventory,product',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = strtolower($validated['product']);

        // Create inventory item
        $inventory = Inventory::create([
            'product' => $product,
            'qty' => $validated['quantity'],
        ]);

        // Record history
        InventoryHistory::create([
            'product' => $product,
            'action' => 'IN',
            'quantity' => $validated['quantity'],
            'remaining_stock' => $validated['quantity'],
            'notes' => 'New product added',
        ]);

        return redirect()->route('inventory')
            ->with('success', 'New product added successfully!');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'product' => 'required|string',
            'action' => 'required|in:restock,consume',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = strtolower($validated['product']);
        $inventory = Inventory::where('product', $product)->firstOrFail();

        if ($validated['action'] === 'restock') {
            $inventory->increment('qty', $validated['quantity']);

            InventoryHistory::create([
                'product' => $product,
                'action' => 'IN',
                'quantity' => $validated['quantity'],
                'remaining_stock' => $inventory->qty,
                'notes' => 'Restock',
            ]);

            return redirect()->route('inventory')
                ->with('success', "Restocked {$validated['quantity']} sacks of " . ucfirst($product) . "!");
        }

        if ($validated['action'] === 'consume') {
            if ($inventory->qty >= $validated['quantity']) {
                $inventory->decrement('qty', $validated['quantity']);

                InventoryHistory::create([
                    'product' => $product,
                    'action' => 'OUT',
                    'quantity' => $validated['quantity'],
                    'remaining_stock' => $inventory->qty,
                    'notes' => 'Stock consumed',
                ]);

                return redirect()->route('inventory')
                    ->with('success', "Used {$validated['quantity']} sacks of " . ucfirst($product) . "!");
            }

            return redirect()->route('inventory')
                ->with('error', 'Not enough stock to consume!');
        }
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return redirect()->route('inventory')
            ->with('success', 'Product deleted successfully!');
    }
}