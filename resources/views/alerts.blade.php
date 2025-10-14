{{-- resources/views/alerts.blade.php --}}
@extends('layouts.app')

@section('title', 'System Alerts')

@section('content')
<style>
  .alerts-container {
    max-width: 1400px;
    margin: 0 auto;
  }
  
  .filter-controls {
    background: rgba(255,255,255,0.95);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    display: flex;
    gap: 15px;
    align-items: center;
  }
  
  .filter-controls label {
    font-weight: bold;
  }
  
  .filter-controls select {
    padding: 8px 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
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
  }
  
  .priority-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
  }
  
  .priority-critical {
    background: #fadbd8;
    color: #e74c3c;
    animation: pulse 2s infinite;
  }
  
  .priority-high {
    background: #fef9e7;
    color: #f39c12;
  }
  
  .priority-medium {
    background: #ebf3fd;
    color: #3498db;
  }
  
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
  }
  
  .alert-type {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
  }
  
  .type-inventory { background: #e8f5e8; color: #27ae60; }
  .type-accounts { background: #fff3cd; color: #856404; }
  .type-delivery { background: #cff4fc; color: #055160; }
</style>

<div class="alerts-container">
  <h1>🚨 System Alerts</h1>
  
  <!-- Filter Controls -->
  <div class="filter-controls">
    <label for="filterType">Filter by Type:</label>
    <select id="filterType" onchange="filterAlerts()">
      <option value="all">All Types</option>
      <option value="Accounts Payable">Accounts Payable</option>
      <option value="Inventory">Inventory</option>
      <option value="Delivery">Delivery</option>
    </select>
    
    <label for="filterPriority">Priority:</label>
    <select id="filterPriority" onchange="filterAlerts()">
      <option value="all">All Priorities</option>
      <option value="high">Critical/High</option>
      <option value="medium">Warning/Medium</option>
    </select>
  </div>
  
  <!-- Alerts Table -->
  <div class="alerts-table">
    <h2 class="table-header">Active Alerts ({{ count($alerts_data ?? []) }})</h2>
    
    <table id="alertsTable" style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Priority</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Type</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Message</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Time</th>
        </tr>
      </thead>
      <tbody>
        @forelse($alerts_data ?? [] as $alert)
        <tr class="alert-row" data-type="{{ $alert['type'] }}" data-priority="{{ $alert['priority'] ?? 'medium' }}" style="border-bottom: 1px solid #eee;">
          <td style="padding: 15px;">
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
          <td style="padding: 15px;">
            <span class="alert-type type-{{ strtolower(str_replace(' ', '-', $alert['type'])) }}">
              {{ $alert['type'] }}
            </span>
          </td>
          <td style="padding: 15px;">{{ $alert['message'] }}</td>
          <td style="padding: 15px;">
            <small style="color: #7f8c8d;">Just now</small>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" style="text-align: center; padding: 60px 20px; color: #7f8c8d;">
            <div style="font-size: 48px; margin-bottom: 20px;">✅</div>
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
  // Filter alerts by type and priority
  function filterAlerts() {
    const typeFilter = document.getElementById('filterType').value;
    const priorityFilter = document.getElementById('filterPriority').value;
    const rows = document.querySelectorAll('.alert-row');
    
    rows.forEach(row => {
      const type = row.dataset.type;
      const priority = row.dataset.priority;
      
      let showByType = typeFilter === 'all' || type === typeFilter;
      let showByPriority = priorityFilter === 'all' || 
                          (priorityFilter === 'high' && (priority === 'high' || row.querySelector('.priority-critical'))) ||
                          (priorityFilter === 'medium' && priority === 'medium');
      
      row.style.display = (showByType && showByPriority) ? '' : 'none';
    });
  }
</script>
@endsection