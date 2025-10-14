{{-- resources/views/delivery.blade.php --}}
@extends('layouts.app')

@section('title', 'Delivery Management')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
  <h1>🚚 Delivery Management</h1>
  
  <!-- Add Delivery Form -->
  <div style="background: rgba(255,255,255,0.95); padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <h2>Schedule New Delivery</h2>
    <form method="POST" action="{{ route('delivery.add') }}">
      @csrf
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Company Name *</label>
          <input type="text" name="company" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Contact Person</label>
          <input type="text" name="contact_person" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Phone</label>
          <input type="tel" name="phone" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
      </div>
      
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Product *</label>
          <select name="product" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
            <option value="">Select Product</option>
            <option value="Ordinary Charcoal">Ordinary Charcoal</option>
            <option value="Special Charcoal">Special Charcoal</option>
            <option value="Premium Charcoal">Premium Charcoal</option>
            <option value="BBQ Charcoal">BBQ Charcoal</option>
          </select>
        </div>
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Quantity (sacks) *</label>
          <input type="number" name="qty" min="1" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Priority</label>
          <select name="priority" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
            <option value="Medium">Medium</option>
            <option value="High">High</option>
            <option value="Low">Low</option>
          </select>
        </div>
      </div>
      
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Delivery Date *</label>
          <input type="date" name="delivery_date" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Delivery Time</label>
          <input type="time" name="delivery_time" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: bold;">Driver</label>
          <input type="text" name="driver" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
      </div>
      
      <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Delivery Address</label>
        <textarea name="address" rows="2" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;"></textarea>
      </div>
      
      <button type="submit" class="btn-primary">Schedule Delivery</button>
    </form>
  </div>
  
  <!-- Deliveries Table -->
  <div style="background: rgba(255,255,255,0.95); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <h2 style="background: #e74c3c; color: white; padding: 20px; margin: 0;">Delivery Schedule</h2>
    
    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Company</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Product</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Quantity</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Delivery Date</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Status</th>
          <th style="background: #34495e; color: white; padding: 15px; text-align: left;">Driver</th>
        </tr>
      </thead>
      <tbody>
        @forelse($delivery_data as $d)
        <tr style="border-bottom: 1px solid #eee;">
          <td style="padding: 15px;">
            <strong>{{ $d->company }}</strong>
            @if($d->contact_person)
            <br><small>{{ $d->contact_person }}</small>
            @endif
          </td>
          <td style="padding: 15px;">{{ $d->product }}</td>
          <td style="padding: 15px;">{{ $d->qty }} sacks</td>
          <td style="padding: 15px;">
            {{ $d->delivery_date ? \Carbon\Carbon::parse($d->delivery_date)->format('Y-m-d') : '-' }}
            @if($d->delivery_time)
            <br><small>{{ $d->delivery_time }}</small>
            @endif
          </td>
          <td style="padding: 15px;">
            <span style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; 
            @if($d->status == 'Delivered') background: #d5f4e6; color: #27ae60;
            @else background: #fef9e7; color: #f39c12; @endif">
              {{ $d->status }}
            </span>
          </td>
          <td style="padding: 15px;">{{ $d->driver ?? '-' }}</td>
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