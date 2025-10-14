<?php
// app/Http/Controllers/AlertsController.php

namespace App\Http\Controllers;

use App\Models\AccountPayable;
use App\Models\Inventory;
use App\Models\Delivery;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlertsController extends Controller
{
    public function index()
    {
        $alerts_data = [];
        $today = Carbon::today();

        // Accounts Payable Alerts
        $accounts = AccountPayable::where('status', '!=', 'paid')->get();
        
        foreach ($accounts as $acc) {
            $dueDate = Carbon::parse($acc->due_date);
            $daysUntilDue = $today->diffInDays($dueDate, false);
            
            if ($daysUntilDue < 0) {
                // Overdue
                $alerts_data[] = [
                    'type' => 'Accounts Payable',
                    'priority' => 'high',
                    'message' => "{$acc->company} payment is " . abs($daysUntilDue) . " days overdue (₱" . number_format($acc->payable, 2) . ")"
                ];
            } elseif ($daysUntilDue <= 7) {
                // Due soon
                $alerts_data[] = [
                    'type' => 'Accounts Payable',
                    'priority' => 'medium',
                    'message' => "{$acc->company} payment due in {$daysUntilDue} days (₱" . number_format($acc->payable, 2) . ")"
                ];
            }
        }

        // Inventory Alerts
        $inventory = Inventory::all();
        
        foreach ($inventory as $item) {
            if ($item->qty == 0) {
                $alerts_data[] = [
                    'type' => 'Inventory',
                    'priority' => 'high',
                    'message' => ucfirst($item->product) . " Charcoal is OUT OF STOCK!"
                ];
            } elseif ($item->qty < 5) {
                $alerts_data[] = [
                    'type' => 'Inventory',
                    'priority' => 'medium',
                    'message' => ucfirst($item->product) . " Charcoal is running low ({$item->qty} sacks left)"
                ];
            }
        }

        // Delivery Alerts
        $deliveries = Delivery::whereNotIn('status', ['Delivered', 'Cancelled'])->get();
        
        foreach ($deliveries as $delivery) {
            $alerts_data[] = [
                'type' => 'Delivery',
                'priority' => 'medium',
                'message' => "Pending delivery: {$delivery->company} - {$delivery->product} ({$delivery->qty} sacks)"
            ];
        }

        return view('alerts', compact('alerts_data'));
    }
}