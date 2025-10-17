{{-- resources/views/delivery.blade.php --}}
@extends('layouts.app')

@section('title', 'Delivery Management')

@section('content')
<style>
  /* ✅ Responsive layout improvements */
  .delivery-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
  }

  .delivery-form {
    background: rgba(255,255,255,0.95);
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

  .form-grid label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
  }

  .form-grid input,
  .form-grid select,
  .form-grid textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
  }

  .btn-primary {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
  }

  .btn-primary:hover {
    background: #c0392b;
  }

  .delivery-table {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .delivery-table h2 {
    background: #e74c3c;
    color: white;
    padding: 20px;
    margin: 0;
  }

  .delivery-table table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
  }

  .delivery-table th {
    background: #34495e;
    color: white;
    padding: 15px;
    text-align: left;
    font-size: 14px;
  }

  .delivery-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
  }

  .status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    display: inline-block;
  }

  /* ✅ Mobile Responsiveness */
  @media (max-width: 1024px) {
    .form-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
    }

    .delivery-container h1 {
      font-size: 22px;
      text-align: center;
    }

    .delivery-form h2 {
      font-size: 18px;
      text-align: center;
    }

    .btn-primary {
      width: 100%;
      text-align: center;
    }

    .delivery-table h2 {
      font-size: 18px;
      text-align: center;
    }

    .delivery-table {
      overflow-x: auto;
      border-radius: 12px;
    }

    .delivery-table table {
      display: block;
      overflow-x: auto;
      white-space: nowrap;
    }

    .delivery-table th, .delivery-table td {
      font-size: 13px;
      padding: 10px;
    }
  }

  @media (max-width: 480px) {
    .form-grid {
      gap: 12px;
    }

    .btn-primary {
      padding: 10px 18px;
      font-size: 13px;
    }

    .delivery-table th, .delivery-table td {
      font-size: 12px;
      padding: 8px;
    }
  }
</style>

<div class="delivery-container">
  <h1>🚚 Delivery Management</h1>
  
  <!-- Add Delivery Form -->
  <div class="delivery-form">
    <h2>Schedule New Delivery</h2>
    <form method="POST" action="{{ route('delivery.add') }}">
      @csrf
      <div class="form-grid">
        <div>
          <label>Company Name *</label>
          <input type="text" name="company" required>
        </div>
        <div>
          <label>Contact Person</label>
          <input type="text" name="contact_person">
        </div>
        <div>
          <label>Phone</label>
          <input type="tel" name="phone">
        </div>
      </div>
      
      <div class="form-grid">
        <div>
          <label>Product *</label>
          <select name="product" required>
            <option value="">Select Product</option>
            <option value="Ordinary Charcoal">Ordinary Charcoal</option>
            <option value="Special Charcoal">Special Charcoal</option>
            <option value="Premium Charcoal">Premium Charcoal</option>
            <option value="BBQ Charcoal">BBQ Charcoal</option>
          </select>
        </div>
        <div>
          <label>Quantity (sacks) *</label>
          <input type="number" name="qty" min="1" required>
        </div>
        <div>
          <label>Priority</label>
          <select name="priority">
            <option value="Medium">Medium</option>
            <option value="High">High</option>
            <option value="Low">Low</option>
          </select>
        </div>
      </div>
      
      <div class="form-grid">
        <div>
          <label>Delivery Date *</label>
          <input type="date" name="delivery_date" required>
        </div>
        <div>
          <label>Delivery Time</label>
          <input type="time" name="delivery_time">
        </div>
        <div>
          <label>Driver</label>
          <input type="text" name="driver">
        </div>
      </div>
      
      <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Delivery Address</label>
        <textarea name="address" rows="2"></textarea>
      </div>
      
      <button type="submit" class="btn-primary">Schedule Delivery</button>
    </form>
  </div>
  
  <!-- Deliveries Table -->
  <div class="delivery-table">
    <h2>Delivery Schedule</h2>
    <table>
      <thead>
        <tr>
          <th>Company</th>
          <th>Product</th>
          <th>Quantity</th>
          <th>Delivery Date</th>
          <th>Status</th>
          <th>Driver</th>
        </tr>
      </thead>
      <tbody>
        @forelse($delivery_data as $d)
        <tr>
          <td>
            <strong>{{ $d->company }}</strong>
            @if($d->contact_person)
              <br><small>{{ $d->contact_person }}</small>
            @endif
          </td>
          <td>{{ $d->product }}</td>
          <td>{{ $d->qty }} sacks</td>
          <td>
            {{ $d->delivery_date ? \Carbon\Carbon::parse($d->delivery_date)->format('Y-m-d') : '-' }}
            @if($d->delivery_time)
              <br><small>{{ $d->delivery_time }}</small>
            @endif
          </td>
          <td>
            <span class="status-badge" style="
              @if($d->status == 'Delivered') background: #d5f4e6; color: #27ae60;
              @else background: #fef9e7; color: #f39c12; @endif
            ">
              {{ $d->status }}
            </span>
          </td>
          <td>{{ $d->driver ?? '-' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
            No deliveries scheduled yet.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
