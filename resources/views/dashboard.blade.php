{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - CAMTS')

@section('content')
<style>
  .dashboard-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 20px;
  }
  
  .dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    background: rgba(255,255,255,0.95);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .welcome-section h1 {
    margin: 0;
    color: #2c3e50;
    font-size: 28px;
  }
  
  .date-time {
    text-align: right;
    color: #7f8c8d;
  }
  
  .current-time {
    font-size: 24px;
    font-weight: bold;
    color: #e74c3c;
  }
  
  .current-date {
    font-size: 14px;
  }
  
  .kpi-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  
  .kpi-card {
    background: rgba(255,255,255,0.95);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  
  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--accent-color);
  }
  
  .kpi-card.sales { --accent-color: #e74c3c; }
  .kpi-card.inventory { --accent-color: #27ae60; }
  .kpi-card.accounts { --accent-color: #f39c12; }
  .kpi-card.deliveries { --accent-color: #3498db; }
  
  .kpi-value {
    font-size: 36px;
    font-weight: bold;
    color: var(--accent-color);
    margin-bottom: 8px;
  }
  
  .kpi-label {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 10px;
  }
  
  .dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
  }
  
  .dashboard-card {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
  }
  
  .card-header {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .card-header h2 {
    margin: 0;
    font-size: 18px;
  }
  
  .btn-small {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: bold;
    background: rgba(255,255,255,0.2);
    color: white;
    transition: background 0.3s;
    text-decoration: none;
  }
  
  .btn-small:hover {
    background: rgba(255,255,255,0.3);
  }
  
  .chart-container {
    padding: 20px;
    height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .chart-container canvas {
    max-width: 100%;
    max-height: 100%;
  }
  
  .data-table {
    max-height: 200px;
    overflow-y: auto;
  }
  
  .data-table table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .data-table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    color: #2c3e50;
    border-bottom: 2px solid #e9ecef;
    position: sticky;
    top: 0;
  }
  
  .data-table td {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
  }
  
  .data-table tr:hover {
    background: #f8f9fa;
  }
  
  .status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
  }
  
  .status-available {
    background: #d5f4e6;
    color: #27ae60;
  }
  
  .status-low-stock {
    background: #fef9e7;
    color: #f39c12;
  }
  
  .status-out-of-stock {
    background: #fadbd8;
    color: #e74c3c;
  }
  
  .amount {
    font-weight: bold;
    color: #27ae60;
  }
  
  .quick-actions {
    background: rgba(255,255,255,0.95);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }
  
  .actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
  }
  
  .action-button {
    display: flex;
    align-items: center;
    padding: 15px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .action-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    color: white;
  }
  
  .action-button.sales { background: linear-gradient(135deg, #e74c3c, #c0392b); }
  .action-button.inventory { background: linear-gradient(135deg, #27ae60, #229954); }
  .action-button.accounts { background: linear-gradient(135deg, #f39c12, #e67e22); }
  .action-button.delivery { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
  
  .action-icon {
    font-size: 24px;
    margin-right: 12px;
  }
  
  .action-text {
    font-weight: bold;
  }
</style>

<div class="dashboard-container">
  <!-- Header -->
  <div class="dashboard-header">
    <div class="welcome-section">
      <h1>📊 Dashboard Overview</h1>
      <p>Welcome back! Here's what's happening with your business today.</p>
    </div>
    <div class="date-time">
      <div class="current-time" id="currentTime"></div>
      <div class="current-date" id="currentDate"></div>
    </div>
  </div>
  
  <!-- KPI Cards -->
  <div class="kpi-section">
    <div class="kpi-card sales">
      <div class="kpi-value">₱{{ number_format($total_sales ?? 0, 2) }}</div>
      <div class="kpi-label">Total Sales</div>
    </div>
    <div class="kpi-card inventory">
      <div class="kpi-value">{{ $total_inventory ?? 0 }}</div>
      <div class="kpi-label">Total Stock (sacks)</div>
    </div>
    <div class="kpi-card accounts">
      <div class="kpi-value">₱{{ number_format($total_payable ?? 0, 2) }}</div>
      <div class="kpi-label">Outstanding Payables</div>
    </div>
    <div class="kpi-card deliveries">
      <div class="kpi-value">{{ count($delivery_data ?? []) }}</div>
      <div class="kpi-label">Active Deliveries</div>
    </div>
  </div>
  
  <!-- Quick Actions -->
  <div class="quick-actions">
    <h3>⚡ Quick Actions</h3>
    <div class="actions-grid">
      <a href="{{ route('sales') }}" class="action-button sales">
        <span class="action-icon">💰</span>
        <span class="action-text">Record Sale</span>
      </a>
      <a href="{{ route('inventory') }}" class="action-button inventory">
        <span class="action-icon">📦</span>
        <span class="action-text">Manage Inventory</span>
      </a>
      <a href="{{ route('accounts') }}" class="action-button accounts">
        <span class="action-icon">📑</span>
        <span class="action-text">Pay Bills</span>
      </a>
      <a href="{{ route('delivery') }}" class="action-button delivery">
        <span class="action-icon">🚚</span>
        <span class="action-text">Schedule Delivery</span>
      </a>
    </div>
  </div>
  
  <!-- Main Dashboard Grid -->
  <div class="dashboard-grid">
    <!-- Sales Card -->
    <div class="dashboard-card">
      <div class="card-header">
        <h2>💰 Recent Sales</h2>
        <div class="card-actions">
          <a href="{{ route('sales') }}" class="btn-small">View All</a>
        </div>
      </div>
      <div class="chart-container">
        <canvas id="salesChart"></canvas>
      </div>
      <div class="data-table">
        <table>
          <thead>
            <tr>
              <th>Company</th>
              <th>Product</th>
              <th>Amount</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($sales_data->take(5) as $sale)
            <tr>
              <td>{{ $sale->company }}</td>
              <td>{{ $sale->product ?? 'Standard Charcoal' }}</td>
              <td class="amount">₱{{ number_format($sale->amount, 2) }}</td>
              <td>{{ $sale->created_at->format('m/d') }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="4" style="text-align: center; padding: 20px; color: #666;">
                No sales recorded yet.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Inventory Card -->
    <div class="dashboard-card">
      <div class="card-header">
        <h2>📦 Inventory Status</h2>
        <div class="card-actions">
          <a href="{{ route('inventory') }}" class="btn-small">Manage</a>
        </div>
      </div>
      <div class="chart-container">
        <canvas id="inventoryChart"></canvas>
      </div>
      <div class="data-table">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Quantity</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($inventory_data as $item)
            <tr>
              <td>{{ ucfirst($item->product) }} Charcoal</td>
              <td>{{ $item->qty }} sacks</td>
              <td>
                @if($item->qty == 0)
                <span class="status-badge status-out-of-stock">Out of Stock</span>
                @elseif($item->qty < 5)
                <span class="status-badge status-low-stock">Low Stock</span>
                @else
                <span class="status-badge status-available">Available</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" style="text-align: center; padding: 20px; color: #666;">
                No inventory items.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Accounts Payable Card -->
    <div class="dashboard-card">
      <div class="card-header">
        <h2>📑 Accounts Payable</h2>
        <div class="card-actions">
          <a href="{{ route('accounts') }}" class="btn-small">Pay Bills</a>
        </div>
      </div>
      <div class="chart-container">
        <canvas id="accountsChart"></canvas>
      </div>
      <div class="data-table">
        <table>
          <thead>
            <tr>
              <th>Company</th>
              <th>Amount</th>
              <th>Due Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($accounts_data->take(5) as $acc)
            <tr>
              <td>{{ $acc->company }}</td>
              <td class="amount">₱{{ number_format($acc->payable, 2) }}</td>
              <td>
                {{ \Carbon\Carbon::parse($acc->due_date)->format('Y-m-d') }}
                @if($acc->days_overdue > 0)
                <span style="color: #e74c3c; font-weight: bold;">(Overdue)</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" style="text-align: center; padding: 20px; color: #666;">
                No accounts payable.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Deliveries Card -->
    <div class="dashboard-card">
      <div class="card-header">
        <h2>🚚 Recent Deliveries</h2>
        <div class="card-actions">
          <a href="{{ route('delivery') }}" class="btn-small">Schedule</a>
        </div>
      </div>
      <div class="chart-container">
        <canvas id="deliveryChart"></canvas>
      </div>
      <div class="data-table">
        <table>
          <thead>
            <tr>
              <th>Company</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($delivery_data->take(5) as $d)
            <tr>
              <td>{{ $d->company }}</td>
              <td>{{ $d->product }}</td>
              <td>{{ $d->qty }} sacks</td>
              <td>
                @if($d->status == 'Delivered')
                <span class="status-badge status-available">{{ $d->status }}</span>
                @else
                <span class="status-badge status-low-stock">{{ $d->status }}</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" style="text-align: center; padding: 20px; color: #666;">
                No deliveries recorded.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Update time and date
  function updateDateTime() {
    const now = new Date();
    document.getElementById('currentTime').textContent = now.toLocaleTimeString();
    document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
  
  setInterval(updateDateTime, 1000);
  updateDateTime();
  
  // Chart configurations
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false
      }
    }
  };
  
  // Sales Chart
  new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
      labels: [@foreach($sales_data->take(5) as $sale)"{{ Str::limit($sale->company, 10) }}",@endforeach],
      datasets: [{
        label: 'Sales Amount',
        data: [@foreach($sales_data->take(5) as $sale){{ $sale->amount }},@endforeach],
        backgroundColor: 'rgba(231, 76, 60, 0.8)',
        borderRadius: 4
      }]
    },
    options: chartOptions
  });
  
  // Inventory Chart
  new Chart(document.getElementById('inventoryChart'), {
    type: 'doughnut',
    data: {
      labels: [@foreach($inventory_data as $item)"{{ ucfirst($item->product) }}",@endforeach],
      datasets: [{
        data: [@foreach($inventory_data as $item){{ $item->qty }},@endforeach],
        backgroundColor: [
          'rgba(231, 76, 60, 0.8)',
          'rgba(52, 152, 219, 0.8)',
          'rgba(46, 204, 113, 0.8)',
          'rgba(155, 89, 182, 0.8)'
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
  
  // Accounts Chart
  new Chart(document.getElementById('accountsChart'), {
    type: 'bar',
    data: {
      labels: [@foreach($accounts_data->take(5) as $acc)"{{ Str::limit($acc->company, 10) }}",@endforeach],
      datasets: [{
        label: 'Payable Amount',
        data: [@foreach($accounts_data->take(5) as $acc){{ $acc->payable }},@endforeach],
        backgroundColor: 'rgba(243, 156, 18, 0.8)',
        borderRadius: 4
      }]
    },
    options: chartOptions
  });
  
  // Delivery Chart
  new Chart(document.getElementById('deliveryChart'), {
    type: 'bar',
    data: {
      labels: [@foreach($delivery_data->take(5) as $d)"{{ Str::limit($d->company, 10) }}",@endforeach],
      datasets: [{
        label: 'Delivery Quantity',
        data: [@foreach($delivery_data->take(5) as $d){{ $d->qty }},@endforeach],
        backgroundColor: 'rgba(52, 152, 219, 0.8)',
        borderRadius: 4
      }]
    },
    options: chartOptions
  });
</script>
@endsection