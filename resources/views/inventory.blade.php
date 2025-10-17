{{-- resources/views/inventory.blade.php --}}
@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<style>
  .inventory-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
  }

  h1 {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 25px;
  }

  /* Alerts */
  .low-stock-alert, .out-of-stock-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }

  .low-stock-alert {
    background: #fffbea;
    border-left: 6px solid #f1c40f;
    color: #856404;
  }

  .out-of-stock-alert {
    background: #fdecea;
    border-left: 6px solid #e74c3c;
    color: #721c24;
    animation: pulseAlert 1.5s infinite;
  }

  @keyframes pulseAlert {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
  }

  /* Summary */
  .inventory-summary {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }

  .summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    text-align: center;
  }

  .summary-item h3 {
    color: #2c3e50;
    margin-bottom: 8px;
  }

  .summary-value {
    font-size: 1.5rem;
    font-weight: bold;
  }

  /* Product Cards */
  .inventory-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 30px;
    justify-items: center;
    margin-bottom: 40px;
  }

  .product-card {
    width: 100%;
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
  }

  .product-card:hover {
    transform: translateY(-3px);
  }

  .product-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .product-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
  }

  .stock-level {
    padding: 8px 18px;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
  }

  .stock-high { background: #eafaf1; color: #27ae60; }
  .stock-medium { background: #fff8e1; color: #f39c12; }
  .stock-low { background: #fdecea; color: #e74c3c; }
  .stock-out { background: #f8d7da; color: #721c24; }

  /* Forms */
  .restock-form {
    display: flex;
    gap: 10px;
    margin-top: 10px;
  }

  .restock-input {
    flex: 1;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
  }

  .restock-input:focus {
    border-color: #e74c3c;
    outline: none;
  }

  .btn {
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
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

  /* Inventory History */
  .inventory-history {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .history-header {
    background: #e74c3c;
    color: white;
    padding: 18px 25px;
    font-size: 1.2rem;
    font-weight: bold;
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
    background: #f9fafb;
  }

  .action-in { color: #27ae60; font-weight: bold; }
  .action-out { color: #e74c3c; font-weight: bold; }

  @media (max-width: 768px) {
    .restock-form {
      flex-direction: column;
    }

    .inventory-grid {
      grid-template-columns: 1fr;
    }
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
        <div class="summary-value" style="color:#3498db;">{{ $inventory_data->count() }}</div>
      </div>
      <div class="summary-item">
        <h3>Total Stock</h3>
        <div class="summary-value" style="color:#27ae60;">{{ $inventory_data->sum('qty') }} sacks</div>
      </div>
      <div class="summary-item">
        <h3>Low Stock Items</h3>
        <div class="summary-value" style="color:#f39c12;">{{ $inventory_data->filter(fn($i)=>$i->qty>0 && $i->qty<5)->count() }}</div>
      </div>
      <div class="summary-item">
        <h3>Out of Stock</h3>
        <div class="summary-value" style="color:#e74c3c;">{{ $inventory_data->filter(fn($i)=>$i->qty==0)->count() }}</div>
      </div>
    </div>
  </div>

  <!-- Product Grid -->
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
          <input type="number" name="quantity" class="restock-input" placeholder="Add sacks" min="1" required>
          <button type="submit" class="btn btn-restock">Restock</button>
        </form>

        <!-- Consume Form -->
        <form method="POST" action="{{ route('inventory.update') }}" class="restock-form">
          @csrf
          <input type="hidden" name="product" value="{{ $item->product }}">
          <input type="hidden" name="action" value="consume">
          <input type="number" name="quantity" class="restock-input" placeholder="Remove sacks" min="1" max="{{ $item->qty }}" required>
          <button type="submit" class="btn btn-consume">Use Stock</button>
        </form>
      </div>
    @endforeach

    <!-- Add Product Card -->
    <div class="product-card">
      <h3>Add New Product</h3>
      <form method="POST" action="{{ route('inventory.add') }}">
        @csrf
        <div style="margin-bottom: 15px;">
          <label>Product Name:</label>
          <input type="text" name="product" class="restock-input" placeholder="e.g., Premium, BBQ, Restaurant" required>
        </div>
        <div style="margin-bottom: 15px;">
          <label>Initial Stock:</label>
          <input type="number" name="quantity" class="restock-input" placeholder="Number of sacks" min="0" required>
        </div>
        <button type="submit" class="btn" style="background:#3498db; color:white; width:100%;">Add Product</button>
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
          <td class="{{ $activity->action == 'IN' ? 'action-in' : 'action-out' }}">{{ $activity->action }}</td>
          <td>{{ $activity->quantity }} sacks</td>
          <td>{{ $activity->remaining_stock }} sacks</td>
          <td>{{ $activity->notes ?? '-' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center; padding:20px; color:#777;">No inventory activity recorded yet.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', e => {
      const action = form.querySelector('input[name="action"]').value;
      const qty = form.querySelector('input[name="quantity"]').value;
      if (!confirm(`Are you sure you want to ${action} ${qty} sacks?`)) e.preventDefault();
    });
  });
});
</script>
@endsection
