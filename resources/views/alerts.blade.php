{{-- resources/views/alerts.blade.php --}}
@extends('layouts.app')

@section('title', 'System Alerts')

@section('content')
<style>
  .alerts-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
  }

  h1 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 2rem;
    color: #2c3e50;
  }

  .filter-controls {
    background: rgba(255,255,255,0.95);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    align-items: center;
  }

  .filter-controls label {
    font-weight: 600;
    color: #2c3e50;
  }

  .filter-controls select {
    padding: 8px 14px;
    border: 2px solid #dcdde1;
    border-radius: 8px;
    background: white;
    transition: all 0.2s ease;
  }

  .filter-controls select:hover {
    border-color: #3498db;
  }

  .alerts-table {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .table-header {
    background: #e74c3c;
    color: white;
    padding: 20px;
    font-size: 1.2rem;
    text-align: center;
    letter-spacing: 0.5px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    padding: 15px 20px;
    text-align: left;
  }

  th {
    background: #34495e;
    color: white;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
  }

  tbody tr:nth-child(even) {
    background: #f9f9f9;
  }

  tbody tr:hover {
    background: #f1f9ff;
  }

  .priority-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    display: inline-block;
  }

  .priority-critical {
    background: #fadbd8;
    color: #c0392b;
    animation: pulse 2s infinite;
  }

  .priority-high {
    background: #fef9e7;
    color: #f39c12;
  }

  .priority-medium {
    background: #ebf3fd;
    color: #2980b9;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
  }

  .alert-type {
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
  }

  .type-inventory { background: #e8f5e8; color: #27ae60; }
  .type-accounts { background: #fff3cd; color: #856404; }
  .type-delivery { background: #cff4fc; color: #055160; }

  /* Empty state styling */
  .no-alerts {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
  }

  .no-alerts div {
    font-size: 48px;
    margin-bottom: 15px;
  }

  .no-alerts h3 {
    margin-bottom: 8px;
  }

  @media (max-width: 768px) {
    h1 {
      font-size: 1.6rem;
    }

    th, td {
      padding: 10px;
      font-size: 14px;
    }

    .filter-controls {
      flex-direction: column;
      gap: 10px;
    }
  }
</style>

<div class="alerts-container">
  <h1>🚨 System Alerts</h1>

  <!-- Filter Controls -->
  <div class="filter-controls">
    <label for="filterType">Type:</label>
    <select id="filterType" onchange="filterAlerts()">
      <option value="all">All Types</option>
      <option value="Accounts Payable">Accounts Payable</option>
      <option value="Inventory">Inventory</option>
      <option value="Delivery">Delivery</option>
    </select>

    <label for="filterPriority">Priority:</label>
    <select id="filterPriority" onchange="filterAlerts()">
      <option value="all">All Priorities</option>
      <option value="high">Critical / High</option>
      <option value="medium">Warning / Medium</option>
    </select>
  </div>

  <!-- Alerts Table -->
  <div class="alerts-table">
    <h2 class="table-header">Active Alerts ({{ count($alerts_data ?? []) }})</h2>

    <table id="alertsTable">
      <thead>
        <tr>
          <th>Priority</th>
          <th>Type</th>
          <th>Message</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody>
        @forelse($alerts_data ?? [] as $alert)
          <tr class="alert-row" 
              data-type="{{ $alert['type'] }}" 
              data-priority="{{ $alert['priority'] ?? 'medium' }}">
            <td>
              <span class="priority-badge 
                @if(str_contains($alert['message'], 'OUT OF STOCK')) priority-critical
                @elseif($alert['priority'] == 'high' || str_contains($alert['message'], 'overdue')) priority-high
                @else priority-medium
                @endif">
                @if(str_contains($alert['message'], 'OUT OF STOCK'))
                  Critical
                @elseif($alert['priority'] == 'high' || str_contains($alert['message'], 'overdue'))
                  High
                @else
                  Warning
                @endif
              </span>
            </td>
            <td>
              <span class="alert-type type-{{ strtolower(str_replace(' ', '-', $alert['type'])) }}">
                {{ $alert['type'] }}
              </span>
            </td>
            <td>{{ $alert['message'] }}</td>
            <td><small style="color: #7f8c8d;">Just now</small></td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="no-alerts">
              <div>✅</div>
              <h3>All Clear!</h3>
              <p>No active alerts at the moment. Your system is running smoothly.</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
  function filterAlerts() {
    const typeFilter = document.getElementById('filterType').value;
    const priorityFilter = document.getElementById('filterPriority').value;
    const rows = document.querySelectorAll('.alert-row');

    rows.forEach(row => {
      const type = row.dataset.type;
      const priority = row.dataset.priority;

      const matchType = typeFilter === 'all' || type === typeFilter;
      const matchPriority =
        priorityFilter === 'all' ||
        (priorityFilter === 'high' && (priority === 'high' || row.querySelector('.priority-critical'))) ||
        (priorityFilter === 'medium' && priority === 'medium');

      row.style.display = matchType && matchPriority ? '' : 'none';
    });
  }
</script>
@endsection
