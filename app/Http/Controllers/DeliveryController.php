<?php
// app/Http/Controllers/DeliveryController.php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function index()
    {
        $delivery_data = Delivery::latest()->get();
        
        // Calculate statistics
        $stats = [
            'delivered' => Delivery::where('status', 'Delivered')->count(),
            'pending' => Delivery::where('status', 'Pending')->count(),
            'in_transit' => Delivery::where('status', 'In Transit')->count(),
            'total' => Delivery::count(),
        ];
        
        // Get available products from inventory
        $available_products = Inventory::pluck('product')->toArray();
        
        return view('delivery', compact('delivery_data', 'stats', 'available_products'));
    }
    
    public function add(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'product' => 'required|string',
            'qty' => 'required|integer|min:1',
            'priority' => 'nullable|string',
            'delivery_date' => 'required|date',
            'delivery_time' => 'nullable',
            'driver' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        // Set default status
        $validated['status'] = 'Pending';
        
        DB::transaction(function () use ($validated) {
            // Create delivery
            Delivery::create($validated);
            
            // Optional: Check inventory and alert if low stock
            $productName = strtolower($validated['product']);
            $inventory = Inventory::where('product', 'like', "%{$productName}%")->first();
            
            if ($inventory && $inventory->qty < $validated['qty']) {
                session()->flash('warning', "Note: Inventory for {$validated['product']} is low ({$inventory->qty} sacks available)");
            }
        });
        
        return redirect()->route('delivery')
            ->with('success', 'Delivery scheduled successfully!');
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'status' => 'required|string|in:Pending,In Transit,Delivered,Cancelled',
            'update_notes' => 'nullable|string',
        ]);
        
        $delivery = Delivery::findOrFail($validated['delivery_id']);
        
        DB::transaction(function () use ($delivery, $validated) {
            $oldStatus = $delivery->status;
            $delivery->status = $validated['status'];
            
            // Append update notes
            if (isset($validated['update_notes'])) {
                $delivery->notes = ($delivery->notes ? $delivery->notes . "\n" : '') . 
                                  "[" . now()->format('Y-m-d H:i') . "] Status changed from {$oldStatus} to {$validated['status']}: " . 
                                  $validated['update_notes'];
            }
            
            $delivery->save();
            
            // If delivery is completed, update inventory
            if ($validated['status'] === 'Delivered' && $oldStatus !== 'Delivered') {
                $productName = strtolower($delivery->product);
                $inventory = Inventory::where('product', 'like', "%{$productName}%")->first();
                
                if ($inventory && $inventory->qty >= $delivery->qty) {
                    $inventory->decrement('qty', $delivery->qty);
                }
            }
        });
        
        return redirect()->route('delivery')
            ->with('success', 'Delivery status updated successfully!');
    }
    
    public function cancel(Request $request)
    {
        $validated = $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
        ]);
        
        $delivery = Delivery::findOrFail($validated['delivery_id']);
        $delivery->status = 'Cancelled';
        $delivery->notes = ($delivery->notes ? $delivery->notes . "\n" : '') . 
                          "[" . now()->format('Y-m-d H:i') . "] Delivery cancelled";
        $delivery->save();
        
        return redirect()->route('delivery')
            ->with('success', 'Delivery cancelled successfully!');
    }
    
    public function show($id)
    {
        $delivery = Delivery::findOrFail($id);
        
        return view('delivery.show', compact('delivery'));
    }
    
    public function destroy($id)
    {
        $delivery = Delivery::findOrFail($id);
        $delivery->delete();
        
        return redirect()->route('delivery')
            ->with('success', 'Delivery record deleted successfully!');
    }
}