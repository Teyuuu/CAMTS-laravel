{{-- resources/views/sales.blade.php --}}
@extends('layouts.app')

@section('title', 'Sales Management')

@section('content')
<style>
  .sales-container {
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .sales-form {
    background: rgba(255,255,255,0.95);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }
  
  .sales-form h2 {
    color: #e74c3c;
    margin-bottom: 20px;
  }
  
  .form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
  }
  
  .form-group {
    flex: 1;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
  }
  
  .form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s;
  }
  
  .form-group input:focus, .form-group select:focus {
    outline: none;
    border-color: #e74c3c;
  }
  
  .btn-primary {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
  }
  
  .btn-primary:hover {
    background: #c0392b;
  }
  
  .total-section {
    background: rgba(255,255,255,0.95);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    text-align: center;
  }
  
  .total-amount {
    font-size: 24px;
    font-weight: bold;
    color: #e74c3c;
  }
  
  .sales-table {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .table-header {
    background: #e74c3c;
    color: white;
    padding: 20px;
    font-size: 18px;
    font-weight: bold;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  table th {
    background: #34495e;
    color: white;
    padding: 12px;
    text-align: left;
  }
  
  table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
  }
  
  table tr:hover {
    background: #f8f9fa;
  }
  
  .amount {
    font-weight: bold;
    color: #27ae60;
  }
</style>

<div class="sales-container">
  <h1>💰 Sales Management</h1>
  
  <!-- Add Sale Form -->
  <div class="sales-form">
    <h2>Add New Sale</h2>
    <form method="POST" action="{{ route('sales.store') }}">
      @csrf
      <div class="form-row">
        <div class="form-group">
          <label>Company Name *</label>
          <input type="text" name="company" placeholder="Enter company name" required value="{{ old('company') }}">
          @error('company')
            <small style="color: red;">{{ $message }}</small>
          @enderror
        </div>
        <div class="form-group">
          <label>Product Type *</label>
          <select name="product" required>
            <option value="">Select Product</option>
            <option value="Premium Charcoal" {{ old('product') == 'Premium Charcoal' ? 'selected' : '' }}>Premium Charcoal</option>
            <option value="Standard Charcoal" {{ old('product') == 'Standard Charcoal' ? 'selected' : '' }}>Standard Charcoal</option>
            <option value="BBQ Charcoal" {{ old('product') == 'BBQ Charcoal' ? 'selected' : '' }}>BBQ Charcoal</option>
            <option value="Restaurant Grade" {{ old('product') == 'Restaurant Grade' ? 'selected' : '' }}>Restaurant Grade</option>
          </select>
          @error('product')
            <small style="color: red;">{{ $message }}</small>
          @enderror
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label>Quantity (kg) *</label>
          <input type="number" name="quantity" placeholder="Enter quantity" min="1" step="0.01" required value="{{ old('quantity') }}">
          @error('quantity')
            <small style="color: red;">{{ $message }}</small>
          @enderror
        </div>
        <div class="form-group">
          <label>Price per kg (₱) *</label>
          <input type="number" name="price_per_kg" placeholder="Enter price per kg" step="0.01" min="0.01" required value="{{ old('price_per_kg') }}">
          @error('price_per_kg')
            <small style="color: red;">{{ $message }}</small>
          @enderror
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label>Total Amount (₱) *</label>
          <input type="number" name="amount" placeholder="Total will be calculated" step="0.01" required readonly id="totalAmount" value="{{ old('amount') }}">
          @error('amount')
            <small style="color: red;">{{ $message }}</small>
          @enderror
        </div>
        <div class="form-group">
          <label>Payment Method</label>
          <select name="payment_method">
            <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
            <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="Check" {{ old('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
            <option value="Credit" {{ old('payment_method') == 'Credit' ? 'selected' : '' }}>Credit</option>
          </select>
        </div>
      </div>
      
      <button type="submit" class="btn-primary">Add Sale</button>
    </form>
  </div>
  
  <!-- Total Sales Summary -->
  <div class="total-section">
    <h3>Total Sales Today</h3>
    <div class="total-amount">
      ₱{{ number_format($total_today ?? 0, 2) }}
    </div>
  </div>
  
  <!-- Sales Table -->
  <div class="sales-table">
    <h2 class="table-header">Sales History</h2>
    
    <table id="salesTable">
      <thead>
        <tr>
          <th>Company</th>
          <th>Product</th>
          <th>Quantity</th>
          <th>Price/kg</th>
          <th>Amount</th>
          <th>Payment</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sales_data as $sale)
        <tr>
          <td>{{ $sale->company }}</td>
          <td>{{ $sale->product ?? 'N/A' }}</td>
          <td>{{ $sale->quantity ? number_format($sale->quantity, 2) : 'N/A' }} kg</td>
          <td>₱{{ $sale->price_per_kg ? number_format($sale->price_per_kg, 2) : 'N/A' }}</td>
          <td class="amount">₱{{ number_format($sale->amount, 2) }}</td>
          <td>{{ $sale->payment_method ?? 'Cash' }}</td>
          <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
            No sales recorded yet. Add your first sale above!
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
  // Auto-calculate total amount
  function calculateTotal() {
    const quantity = parseFloat(document.querySelector('input[name="quantity"]').value) || 0;
    const pricePerKg = parseFloat(document.querySelector('input[name="price_per_kg"]').value) || 0;
    const total = quantity * pricePerKg;
    document.getElementById('totalAmount').value = total.toFixed(2);
  }
  
  // Add event listeners for auto-calculation
  document.querySelector('input[name="quantity"]').addEventListener('input', calculateTotal);
  document.querySelector('input[name="price_per_kg"]').addEventListener('input', calculateTotal);
</script>
@endsection