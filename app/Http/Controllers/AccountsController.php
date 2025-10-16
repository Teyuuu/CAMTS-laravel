<?php
// app/Http/Controllers/AccountsController.php

namespace App\Http\Controllers;

use App\Models\AccountPayable;
use App\Models\PaymentHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{
    public function index()
    {
        $accounts_data = AccountPayable::where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->get();
        
        // Calculate statistics
        $total_payable = $accounts_data->sum('payable');
        $overdue_amount = 0;
        $due_soon_amount = 0;
        $overdue_count = 0;
        $due_soon_count = 0;
        
        // Calculate days for each account
        foreach ($accounts_data as $acc) {
            $dueDate = Carbon::parse($acc->due_date);
            $today = Carbon::today();
            $daysUntilDue = $today->diffInDays($dueDate, false);
            
            $acc->days_until_due = max(0, $daysUntilDue);
            $acc->days_overdue = $daysUntilDue < 0 ? abs($daysUntilDue) : 0;
            
            if ($daysUntilDue < 0) {
                $overdue_amount += $acc->payable;
                $overdue_count++;
            } elseif ($daysUntilDue <= 7) {
                $due_soon_amount += $acc->payable;
                $due_soon_count++;
            }
        }
        
        // Get payment history
        $payment_history = PaymentHistory::latest()->take(10)->get();
        
        // Calculate paid this month
        $paid_this_month = PaymentHistory::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        return view('accounts', compact(
            'accounts_data',
            'payment_history',
            'total_payable',
            'overdue_amount',
            'due_soon_amount',
            'paid_this_month',
            'overdue_count',
            'due_soon_count'
        ));
    }
    
    public function add(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'invoice_number' => 'nullable|string|max:100',
            'category' => 'nullable|string',
            'priority' => 'nullable|string',
            'payable' => 'required|numeric|min:0',
            'invoice_date' => 'nullable|date',
            'due_date' => 'required|date',
            'payment_terms' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        
        $validated['status'] = 'pending';
        
        AccountPayable::create($validated);
        
        return redirect()->route('accounts')
            ->with('success', 'Account payable added successfully!');
    }
    
    public function pay(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts_payable,id',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        DB::transaction(function () use ($validated) {
            $account = AccountPayable::findOrFail($validated['account_id']);
            
            // Update account status
            $account->status = 'paid';
            $account->save();
            
            // Record payment history
            PaymentHistory::create([
                'account_id' => $account->id,
                'company' => $account->company,
                'invoice_number' => $account->invoice_number,
                'amount' => $account->payable,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'] ?? 'Cash',
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
        });
        
        return redirect()->route('accounts')
            ->with('success', 'Payment recorded successfully!');
    }
    
    public function edit($id)
    {
        $account = AccountPayable::findOrFail($id);
        $accounts_data = AccountPayable::where('status', '!=', 'paid')->get();
        
        return view('accounts.edit', compact('account', 'accounts_data'));
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'invoice_number' => 'nullable|string|max:100',
            'category' => 'nullable|string',
            'priority' => 'nullable|string',
            'payable' => 'required|numeric|min:0',
            'invoice_date' => 'nullable|date',
            'due_date' => 'required|date',
            'payment_terms' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        
        $account = AccountPayable::findOrFail($id);
        $account->update($validated);
        
        return redirect()->route('accounts')
            ->with('success', 'Account updated successfully!');
    }
    
    public function destroy($id)
    {
        $account = AccountPayable::findOrFail($id);
        $account->delete();
        
        return redirect()->route('accounts')
            ->with('success', 'Account deleted successfully!');
    }
}