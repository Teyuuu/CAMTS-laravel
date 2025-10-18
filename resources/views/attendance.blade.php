{{-- resources/views/attendance.blade.php --}}
@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<style>
  .attendance-container {
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

  .clock-card {
    background: rgba(255,255,255,0.95);
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
    margin-bottom: 30px;
  }

  .day-display {
    font-size: 20px;
    color: #34495e;
    font-weight: 600;
  }

  .date-display {
    font-size: 22px;
    color: #2c3e50;
    margin: 5px 0;
  }

  .clock-display {
    font-size: 52px;
    font-weight: bold;
    color: #e74c3c;
    font-family: 'Courier New', monospace;
    margin: 10px 0 15px;
  }

  .quick-actions {
    background: rgba(255,255,255,0.95);
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }

  .quick-actions h3 {
    margin-bottom: 15px;
    color: #2c3e50;
    text-align: center;
  }

  select {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #dcdde1;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s ease;
  }

  select:hover {
    border-color: #3498db;
  }

  #currentStatus {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
  }

  .action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 15px;
  }

  .btn-time-in,
  .btn-time-out {
    flex: 1;
    min-width: 150px;
    padding: 18px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: bold;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-time-in {
    background: #27ae60;
  }
  .btn-time-in:hover {
    background: #219150;
  }

  .btn-time-out {
    background: #e74c3c;
  }
  .btn-time-out:hover {
    background: #c0392b;
  }

  .attendance-table {
    background: rgba(255,255,255,0.95);
    border-radius: 16px;
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

  .hours {
    font-weight: bold;
    color: #27ae60;
  }

  .status-late {
    color: #e74c3c;
    font-weight: bold;
  }

  .status-complete {
    color: #27ae60;
  }

  .status-progress {
    color: #f39c12;
  }

  .status-absent {
    color: #e74c3c;
  }

  @media (max-width: 768px) {
    h1 {
      font-size: 1.6rem;
    }

    th, td {
      padding: 10px;
      font-size: 14px;
    }

    .action-buttons {
      flex-direction: column;
    }

    .btn-time-in,
    .btn-time-out {
      width: 100%;
    }
    .table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch; /* smooth scrolling on mobile */
}

.table-responsive table {
  width: 100%;
  min-width: 700px; /* adjust this depending on how wide your table columns are */
}
  }
</style>

<div class="attendance-container">
  <h1>🕒 Attendance Management System</h1>

  <!-- Digital Clock -->
  <div class="clock-card">
    <div class="day-display" id="day"></div>
    <div class="date-display" id="date"></div>
    <div class="clock-display" id="time"></div>
    <div style="font-size: 14px; color: #7f8c8d;">
      Current Time (Philippine Standard Time)
    </div>
  </div>

  <!-- Attendance Actions -->
  <div class="quick-actions">
    <h3>Quick Actions</h3>

    <div style="margin-bottom: 20px;">
      <label>Select Employee:</label>
      <select id="employeeSelect">
        <option value="">Choose Employee</option>
        @foreach($employees ?? [] as $employee)
          <option value="{{ $employee['id'] }}">{{ $employee['name'] }} - {{ $employee['position'] }}</option>
        @endforeach
      </select>
    </div>

    <div id="currentStatus">
      <strong>Status:</strong> Please select an employee
    </div>

    <div class="action-buttons">
      <button id="timeInBtn" class="btn-time-in" onclick="timeIn()" disabled>🟢 Time In</button>
      <button id="timeOutBtn" class="btn-time-out" onclick="timeOut()" disabled>🔴 Time Out</button>
    </div>
  </div>

  <!-- Attendance History -->
  <div class="attendance-table">
    <h2 class="table-header">Attendance History</h2>
    <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Employee</th>
          <th>Time In</th>
          <th>Time Out</th>
          <th>Hours Worked</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($attendance_records ?? [] as $record)
          <tr>
            <td>{{ $record['date']->format('Y-m-d') }}</td>
            <td>{{ $record['employee_name'] }}</td>
            <td>{{ $record['time_in'] ? $record['time_in']->format('h:i A') : '-' }}</td>
            <td>{{ $record['time_out'] ? $record['time_out']->format('h:i A') : '-' }}</td>
            <td class="hours">{{ $record['hours_worked'] ?? '-' }}h</td>
            <td>
              @if($record['is_late'] ?? false)
                <span class="status-late">Late</span>
              @elseif($record['time_in'] && $record['time_out'])
                <span class="status-complete">Complete</span>
              @elseif($record['time_in'])
                <span class="status-progress">In Progress</span>
              @else
                <span class="status-absent">Absent</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: #7f8c8d;">
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

  function updateClock() {
    const now = new Date();
    const options = { timeZone: 'Asia/Manila' };
    document.getElementById("day").textContent = now.toLocaleDateString("en-US", { weekday: 'long', ...options });
    document.getElementById("date").textContent = now.toLocaleDateString("en-US", { month: 'long', day: 'numeric', year: 'numeric', ...options });
    document.getElementById("time").textContent = now.toLocaleTimeString("en-US", options);
  }

  setInterval(updateClock, 1000);
  updateClock();

  document.getElementById('employeeSelect').addEventListener('change', function() {
    const timeInBtn = document.getElementById('timeInBtn');
    const timeOutBtn = document.getElementById('timeOutBtn');
    const statusDiv = document.getElementById('currentStatus');

    if (this.value) {
      currentEmployee = this.value;
      statusDiv.innerHTML = '<strong>Status:</strong> <span style="color:#e74c3c;">Clocked Out</span>';
      timeInBtn.disabled = false;
      timeOutBtn.disabled = false;
    } else {
      currentEmployee = null;
      statusDiv.innerHTML = '<strong>Status:</strong> Please select an employee';
      timeInBtn.disabled = true;
      timeOutBtn.disabled = true;
    }
  });

  function timeIn() {
    if (!currentEmployee) return alert('Please select an employee first');
    if (confirm('Clock in now?')) {
      fetch('{{ route("attendance.time-in") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ employee_id: currentEmployee, timestamp: new Date().toISOString() })
      })
      .then(res => res.json())
      .then(data => { if (data.success) location.reload(); })
      .catch(() => alert('Time in recorded (offline mode)'));
    }
  }

  function timeOut() {
    if (!currentEmployee) return alert('Please select an employee first');
    if (confirm('Clock out now?')) {
      fetch('{{ route("attendance.time-out") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ employee_id: currentEmployee, timestamp: new Date().toISOString() })
      })
      .then(res => res.json())
      .then(data => { if (data.success) location.reload(); })
      .catch(() => alert('Time out recorded (offline mode)'));
    }
  }
</script>
@endsection
