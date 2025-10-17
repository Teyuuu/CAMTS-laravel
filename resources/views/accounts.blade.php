{{-- resources/views/accounts.blade.php --}}
@extends('layouts.app')

@section('title', 'Accounts Payable')

@section('content')

<style>
  .accounts-container {
    max-width: 1400px;
    margin: 0 auto;
    background: transparent;
  }

  h1 {
    color: #2c3e50;
    margin-bottom: 20px;
    text-align: center;
    font-size: 2rem;
  }

  h2 {
    color: #2c3e50;
    margin-bottom: 15px;
  }

  /* Form Section */
  .accounts-form {
    background: rgba(255, 255, 255, 0.95);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 20px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-group label {
    margin-bottom: 8px;
    font-weight: bold;
    color: #2c3e50;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    border-color: #e74c3c;
    outline: none;
    box-shadow: 0 0 6px rgba(231, 76, 60, 0.3);
  }

  .btn-primary {
    display: inline-block;
    background: #e74c3c;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
  }

  .btn-primary:hover {
    background: #c0392b;
    transform: translateY(-1px);
  }

  /* Table Section */
  .accounts-table {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .table-header {
    background: #e74c3c;
    color: white;
    padding: 20px;
    font-size: 1.2rem;
    font-weight: bold;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    padding: 15px 12px;
    text-align: left;
  }

  table th {
    background: #34495e;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  table tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  table tr:hover {
    background: rgba(231, 76, 60, 0.1);
  }

  .status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  .status-paid {
    background: #d5f4e6;
    color: #27ae60;
  }

  .status-overdue {
    background: #fadbd8;
    color: #e74c3c;
  }

  .status-pending {
    background: #fef9e7;
    color: #f39c12;
  }

  /* Responsive Layout */
  @media (max-width: 1024px) {
    .form-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
    }

    .accounts-form {
      padding: 20px;
    }

    .accounts-container {
      padding: 0 10px;
    }

    table th, table td {
      padding: 10px;
      font-size: 13px;
    }

    h1 {
      font-size: 1.6rem;
    }

    .btn-primary {
      width: 100%;
      text-align: center;
    }

    table {
      display: block;
      overflow-x: auto;
      white-space: nowrap;
    }
  }

  @media (max-width: 480px) {
    h1 {
      font-size: 1.4rem;
    }
    .table-header {
      font-size: 1rem;
    }
  }
</style>

<div class="accounts-container">
  <h1>📑 Accounts Payable Management</h1>

  <!-- Add New Account Form -->
  <div class="accounts-form">
    <h2>Add New Account Payable</h2>
    <form method="POST" action="{{ route('accounts.add') }}">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Company/Vendor Name *</label>
          <input type="text" name="company" required value="{{ old('company') }}">
        </div>
        <div class="form-group">
          <label>Contact Person</label>
          <input type="text" name="contact_person" value="{{ old('contact_person') }}">
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" name="phone" value="{{ old('phone') }}">
        </div>
      </div>

      <div class="form-grid">
        <div class="form-group">
          <label>Invoice Number</label>
          <input type="text" name="invoice_number" value="{{ old('invoice_number') }}">
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category">
            <option value="Supplies">Supplies</option>
            <option value="Equipment">Equipment</option>
            <option value="Services">Services</option>
            <option value="Utilities">Utilities</option>
            <option value="Rent">Rent</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group">
          <label>Priority</label>
          <select name="priority">
            <option value="Medium">Medium</option>
            <option value="High">High</option>
            <option value="Low">Low</option>
          </select>
        </div>
      </div>

      <div class="form-grid">
        <div class="form-group">
          <label>Amount Payable (₱) *</label>
          <input type="number" name="payable" step="0.01" required value="{{ old('payable') }}">
        </div>
        <div class="form-group">
          <label>Invoice Date</label>
          <input type="date" name="invoice_date" value="{{ old('invoice_date') }}">
        </div>
        <div class="form-group">
          <label>Due Date *</label>
          <input type="date" name="due_date" required value="{{ old('due_date') }}">
        </div>
      </div>

      <div class="form-grid">
        <div class="form-group" style="grid-column: 1 / -1;">
          <label>Description/Notes</label>
          <textarea name="description" rows="3">{{ old('description') }}</textarea>
        </div>
      </div>

      <button type="submit" class="btn-primary">Add Account Payable</button>
    </form>
  </div>

  <!-- Accounts Payable Table -->
  <div class="accounts-table">
    <h2 class="table-header">Accounts Payable</h2>
    <table>
      <thead>
        <tr>
          <th>Company</th>
          <th>Invoice #</th>
          <th>Category</th>
          <th>Amount</th>
          <th>Due Date</th>
          <th>Status</th>
          <th>Priority</th>
        </tr>
      </thead>
      <tbody>
        @forelse($accounts_data as $acc)
        <tr>
          <td>
            <strong>{{ $acc->company }}</strong>
            @if($acc->contact_person)
              <br><small>{{ $acc->contact_person }}</small>
            @endif
          </td>
          <td>{{ $acc->invoice_number ?? '-' }}</td>
          <td>{{ $acc->category ?? 'Other' }}</td>
          <td style="font-weight: bold;">₱{{ number_format($acc->payable, 2) }}</td>
          <td>{{ \Carbon\Carbon::parse($acc->due_date)->format('Y-m-d') }}</td>
          <td>
            <span class="status-badge 
              @if($acc->status == 'paid') status-paid
              @elseif(isset($acc->days_overdue) && $acc->days_overdue > 0) status-overdue
              @else status-pending
              @endif">
              @if($acc->status == 'paid') Paid
              @elseif(isset($acc->days_overdue) && $acc->days_overdue > 0) Overdue
              @else Pending
              @endif
            </span>
          </td>
          <td>{{ $acc->priority ?? 'Medium' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
            No accounts payable recorded yet.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection