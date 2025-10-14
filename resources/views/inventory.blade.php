{{-- resources/views/inventory.blade.php --}}
@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<style>
  .inventory-container {
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .inventory-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
  }
  
  .product-card {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.2s;
  }
  
  .product-card:hover {
    transform: translateY(-2px);
  }
  
  .product-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .product-name {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
  }
  
  .stock-level {
    font-size: 28px;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 8px;
    text-align: center;
  }
  
  .stock-high { background: #d5f4e6; color: #27ae60; }
  .stock-medium { background: #fef9e7; color: #f39c12; }
  .stock-low { background: #fadbd8; color: #e74c3c; }
  .stock-out { background: #f8d7da; color: #721c24; }
  
  .restock-form {
    display: flex;
    gap: 10px;
    margin-top: 20px;
  }
  
  .restock-input {
    flex: 1;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
  }
  
  .restock-input:focus {
    outline: none;
    border-color: #e74c3c;
  }
  
  .btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
  }
  
  .btn-restock {
    background: #27ae60;
    color: white;
  }
  
  .btn-restock:hover {
    background: #219a52;
  }
  
  .btn-consume {
    background: #e74c3c;
    color: white;
  }
  
  .btn-consume:hover {
    background: #c0392b;
  }
  
  .inventory-summary {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }
  
  .summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    text-align: center;
  }
  
  .summary-item h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
  }
  
  .summary-value {
    font-size: 24px;
    font-weight: bold;
  }
  
  .inventory-history {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .history-header {
    background: #e74c3c;
    color: white;
    padding: 15px 25px;
    margin: 0;
    font-size: 20px;
  }
  
  .history-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .history-table th {
    background: #34495e;
    color: white;
    padding: 12px;
    text-align: left;
  }
  
  .history-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
  }
  
  .history-table tr:hover {
    background: #f8f9fa;
  }
  
  .action-in { color: #27ae60; font-weight: bold; }
  .action-out { color: #e74c3c; font-weight: bold; }
  
  .low-stock-alert {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
  }
  
  .out-of-stock-alert {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
  }
</style>

<div class="inventory-container">
  <h1>📦 Inventory Management</h1>
  
  <!-- Alerts -->
  @foreach($inventory_data as $item)
    @if($item->qty == 0)
    <div class="out-of-stock-alert">
      <strong>⚠️ OUT OF STOCK:</strong> {{ ucfirst($item->product) }} Charcoal is completely out of stock!
    </div>
    @elseif($item->qty < 5)
    <div class="low-stock-alert">
      <strong>⚠️ LOW STOCK:</strong> {{ ucfirst($item->product) }} Charcoal has only {{ $item->qty }} sacks left.
    </div>
    @endif
  @endforeach
  
  <!-- Inventory Summary -->
  <div class="inventory-summary">
    <h2>Inventory Summary</h2>
    <div class="summary-grid">
      <div class="summary-item">
        <h3>Total Products</h3>
        <div class="summary-value" style="color: #3498db;">{{ $inventory_data->count() }}</div>
      </div>
      <div class="summary-item">
        <h3>Total Stock</h3>
        <div class="summary-value" style="color: #27ae60;">
          {{ $inventory_data->sum('qty') }} sacks
        </div>
      </div>
      <div class="summary-item">
        <h3>Low Stock Items</h3>
        <div class="summary-value" style="color: #f39c12;">
          {{ $inventory_data->filter(fn($item) => $item->qty > 0 && $item->qty < 5)->count() }}
        </div>
      </div>
      <div class="summary-item">
        <h3>Out of Stock</h3>
        <div class="summary-value" style="color: #e74c3c;">
          {{ $inventory_data->filter(fn($item) => $item->qty == 0)->count() }}
        </div>
      </div>
    </div>
  </div>
  
  <!-- Product Cards -->
  <div class="inventory-grid">
    @foreach($inventory_data as $item)
    <div class="product-card">
      <div class="product-header">
        <div class="product-name">{{ ucfirst($item->product) }} Charcoal</div>
        <div class="stock-level 
          @if($item->qty == 0) stock-out
          @elseif($item->qty < 5) stock-low
          @elseif($item->qty < 15) stock-medium
          @else stock-high
          @endif">
          {{ $item->qty }} sacks
        </div>
      </div>
      
      <div class="product-details">
        <p><strong>SKU:</strong> {{ strtoupper($item->product) }}-001</p>
        <p><strong>Category:</strong> Charcoal Products</p>
        <p><strong>Unit:</strong> Sacks (50kg each)</p>
        <p><strong>Status:</strong> 
          @if($item->qty == 0)
            Out of Stock
          @elseif($item->qty < 5)
            Low Stock
          @elseif($item->qty < 15)
            Medium Stock
          @else
            Good Stock
          @endif
        </p>
      </div>
      
      <!-- Restock Form -->
      <form method="POST" action="{{ route('inventory.update') }}" class="restock-form">
        @csrf
        <input type="hidden" name="product" value="{{ $item->product }}">
        <input type="hidden" name="action" value="restock">
        <input type="number" name="quantity" class="restock-input" 
               placeholder="Add sacks" min="1" required>
        <button type="submit" class="btn btn-restock">Restock</button>
      </form>
      
      <!-- Consume Stock Form -->
      <form method="POST" action="{{ route('inventory.update') }}" class="restock-form" style="margin-top: 10px;">
        @csrf
        <input type="hidden" name="product" value="{{ $item->product }}">
        <input type="hidden" name="action" value="consume">
        <input type="number" name="quantity" class="restock-input" 
               placeholder="Remove sacks" min="1" max="{{ $item->qty }}" required>
        <button type="submit" class="btn btn-consume">Use Stock</button>
      </form>
    </div>
    @endforeach
    
    <!-- Add New Product -->
    <div class="product-card">
      <h3>Add New Product</h3>
      <form method="POST" action="{{ route('inventory.add') }}">
        @csrf
        <div style="margin-bottom: 15px;">
          <label>Product Name:</label>
          <input type="text" name="product" class="restock-input" 
                 placeholder="e.g., Premium, BBQ, Restaurant" required>
        </div>
        <div style="margin-bottom: 15px;">
          <label>Initial Stock:</label>
          <input type="number" name="quantity" class="restock-input" 
                 placeholder="Number of sacks" min="0" required>
        </div>
        <button type="submit" class="btn" style="background: #3498db; color: white; width: 100%;">
          Add Product
        </button>
      </form>
    </div>
  </div>
  
  <!-- Inventory History -->
  <div class="inventory-history">
    <h2 class="history-header">Recent Inventory Activity</h2>
    <table class="history-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Product</th>
          <th>Action</th>
          <th>Quantity</th>
          <th>Remaining</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        @forelse($inventory_history as $activity)
        <tr>
          <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
          <td>{{ ucfirst($activity->product) }}</td>
          <td class="{{ $activity->action == 'IN' ? 'action-in' : 'action-out' }}">
            {{ $activity->action }}
          </td>
          <td>{{ $activity->quantity }} sacks</td>
          <td>{{ $activity->remaining_stock }} sacks</td>
          <td>{{ $activity->notes ?? '-' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
            No inventory activity recorded yet.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
  // Add form submission handlers for better UX
  document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
      form.addEventListener('submit', function(e) {
        const action = form.querySelector('input[name="action"]');
        const quantity = form.querySelector('input[name="quantity"]');
        
        if (quantity && quantity.value && action) {
          const actionText = action.value;
          const confirmMessage = `Are you sure you want to ${actionText} ${quantity.value} sacks?`;
          
          if (!confirm(confirmMessage)) {
            e.preventDefault();
          }
        }
      });
    });
  });
</script>
@endsection