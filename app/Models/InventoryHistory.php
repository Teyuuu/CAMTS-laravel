<?php
// app/Models/InventoryHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $table = 'inventory_history';

    protected $fillable = [
        'product',
        'action',
        'quantity',
        'remaining_stock',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'product', 'product');
    }

    // Accessors
    public function getActionLabelAttribute()
    {
        return $this->action === 'IN' ? 'Restock' : 'Consumed';
    }

    public function getActionColorAttribute()
    {
        return $this->action === 'IN' ? '#27ae60' : '#e74c3c';
    }
}