<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Inventory;
use App\Models\AccountPayable;
use App\Models\Delivery;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get data for dashboard
        $sales_data = Sale::latest()->take(10)->get();
        $inventory_data = Inventory::all();
        $accounts_data = AccountPayable::where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();
        $delivery_data = Delivery::whereIn('status', ['Pending', 'In Transit'])
            ->latest()
            ->take(10)
            ->get();

        // Calculate totals for KPI cards
        $total_sales = Sale::sum('amount');
        $total_inventory = Inventory::sum('qty');
        $total_payable = AccountPayable::where('status', '!=', 'paid')->sum('payable');

        // Calculate days until due for accounts
        foreach ($accounts_data as $account) {
            $dueDate = Carbon::parse($account->due_date);
            $today = Carbon::today();
            $account->days_until_due = $dueDate->diffInDays($today, false);
            $account->days_overdue = $account->days_until_due < 0 ? abs($account->days_until_due) : 0;
            $account->days_until_due = max(0, $account->days_until_due);
        }

        return view('dashboard', compact(
            'sales_data',
            'inventory_data',
            'accounts_data',
            'delivery_data',
            'total_sales',
            'total_inventory',
            'total_payable'
        ));
    }
}