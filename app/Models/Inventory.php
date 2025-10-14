<?php
// app/Models/Inventory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'product',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    // Relationships
    public function history()
    {
        return $this->hasMany(InventoryHistory::class, 'product', 'product');
    }

    // Accessors
    public function getStatusAttribute()
    {
        if ($this->qty == 0) {
            return 'out_of_stock';
        } elseif ($this->qty < 5) {
            return 'low_stock';
        }
        return 'available';
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            default => 'Available',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'out_of_stock' => '#e74c3c',
            'low_stock' => '#f39c12',
            default => '#27ae60',
        };
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->where('qty', '<', 5)->where('qty', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('qty', 0);
    }

    // Methods
    public function needsRestock()
    {
        return $this->qty < 5;
    }
}