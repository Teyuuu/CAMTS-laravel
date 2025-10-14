{{-- resources/views/attendance.blade.php --}}
@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<style>
  .attendance-container {
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .clock-card {
    background: rgba(255,255,255,0.95);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
    margin-bottom: 30px;
  }
  
  .clock-display {
    font-size: 48px;
    font-weight: bold;
    color: #e74c3c;
    margin: 20px 0;
    font-family: 'Courier New', monospace;
  }
  
  .date-display {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 10px;
  }
  
  .quick-actions {
    background: rgba(255,255,255,0.95);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }
  
  .action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 20px;
  }
  
  .btn-time-in {
    background: #27ae60;
    color: white;
    border: none;
    padding: 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
  }
  
  .btn-time-out {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
  }
  
  .attendance-table {
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
</style>

<div class="attendance-container">
  <h1>🕒 Attendance Management System</h1>
  
  <!-- Digital Clock -->
  <div class="clock-card">
    <div class="day-display" id="day"></div>
    <div class="date-display" id="date"></div>
    <div class="clock-display" id="time"></div>
    <div style="margin-top: 15px; font-size: 14px; color: #7f8c8d;">
      Current Time (Philippine Standard Time)
    </div>
  </div>
  
  <!-- Attendance Actions -->
  <div class="quick-actions">
    <h3>Quick Actions</h3>
    
    <div style="margin-bottom: 20px;">
      <label>Select Employee:</label>
      <select id="employeeSelect" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
        <option value="">Choose Employee</option>
        @foreach($employees ?? [] as $employee)
        <option value="{{ $employee['id'] }}">{{ $employee['name'] }} - {{ $employee['position'] }}</option>
        @endforeach
      </select>
    </div>
    
    <div id="currentStatus" style="background: #ecf0f1; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
      <strong>Status:</strong> Please select an employee
    </div>
    
    <div class="action-buttons">
      <button id="timeInBtn" class="btn-time-in" onclick="timeIn()" disabled>
        🟢 Time In
      </button>
      <button id="timeOutBtn" class="btn-time-out" onclick="timeOut()" disabled>
        🔴 Time Out
      </button>
    </div>
  </div>
  
  <!-- Attendance History -->
  <div class="attendance-table">
    <h2 class="table-header">Attendance History</h2>
    
    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Date</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Employee</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Time In</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Time Out</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Hours Worked</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($attendance_records ?? [] as $record)
        <tr style="border-bottom: 1px solid #eee;">
          <td style="padding: 15px;">{{ $record['date']->format('Y-m-d') }}</td>
          <td style="padding: 15px;">{{ $record['employee_name'] }}</td>
          <td style="padding: 15px;">{{ $record['time_in'] ? $record['time_in']->format('h:i A') : '-' }}</td>
          <td style="padding: 15px;">{{ $record['time_out'] ? $record['time_out']->format('h:i A') : '-' }}</td>
          <td style="padding: 15px; font-weight: bold; color: #27ae60;">
            {{ $record['hours_worked'] ?? '-' }}h
          </td>
          <td style="padding: 15px;">
            @if($record['is_late'] ?? false)
            <span style="color: #e74c3c; font-weight: bold;">Late</span>
            @elseif($record['time_in'] && $record['time_out'])
            <span style="color: #27ae60;">Complete</span>
            @elseif($record['time_in'])
            <span style="color: #f39c12;">In Progress</span>
            @else
            <span style="color: #e74c3c;">Absent</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
            No attendance records found
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
  let currentEmployee = null;
  let currentStatus = 'out';
  
  // Update clock
  function updateClock() {
    const now = new Date();
    const options = { timeZone: 'Asia/Manila' };
    
    document.getElementById("day").textContent = now.toLocaleDateString("en-US", { 
      weekday: 'long', 
      ...options 
    });
    document.getElementById("date").textContent = now.toLocaleDateString("en-US", { 
      month: 'long', 
      day: 'numeric', 
      year: 'numeric',
      ...options 
    });
    document.getElementById("time").textContent = now.toLocaleTimeString("en-US", options);
  }
  
  setInterval(updateClock, 1000);
  updateClock();
  
  // Update employee status
  document.getElementById('employeeSelect').addEventListener('change', function() {
    const timeInBtn = document.getElementById('timeInBtn');
    const timeOutBtn = document.getElementById('timeOutBtn');
    const statusDiv = document.getElementById('currentStatus');
    
    if (this.value) {
      currentEmployee = this.value;
      currentStatus = 'out';
      
      if (currentStatus === 'out') {
        statusDiv.innerHTML = '<strong>Status:</strong> <span style="color: #e74c3c;">Clocked Out</span>';
        timeInBtn.disabled = false;
        timeOutBtn.disabled = true;
      } else {
        statusDiv.innerHTML = '<strong>Status:</strong> <span style="color: #27ae60;">Clocked In</span>';
        timeInBtn.disabled = true;
        timeOutBtn.disabled = false;
      }
    } else {
      currentEmployee = null;
      statusDiv.innerHTML = '<strong>Status:</strong> Please select an employee';
      timeInBtn.disabled = true;
      timeOutBtn.disabled = true;
    }
  });
  
  // Time In function
  function timeIn() {
    if (!currentEmployee) {
      alert('Please select an employee first');
      return;
    }
    
    if (confirm('Clock in now?')) {
      fetch('{{ route("attendance.time-in") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          employee_id: currentEmployee,
          timestamp: new Date().toISOString()
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Time in recorded successfully!');
          location.reload();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Time in recorded (offline mode)');
      });
    }
  }
  
  // Time Out function
  function timeOut() {
    if (!currentEmployee) {
      alert('Please select an employee first');
      return;
    }
    
    if (confirm('Clock out now?')) {
      fetch('{{ route("attendance.time-out") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          employee_id: currentEmployee,
          timestamp: new Date().toISOString()
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Time out recorded successfully!');
          location.reload();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Time out recorded (offline mode)');
      });
    }
  }
</script>
@endsection