<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPayable extends Model
{
    use HasFactory;

    protected $table = 'accounts_payable';

    protected $fillable = [
        'company',
        'contact_person',
        'phone',
        'invoice_number',
        'category',
        'priority',
        'payable',
        'invoice_date',
        'due_date',
        'payment_terms',
        'description',
        'status',
    ];

    protected $casts = [
        'payable' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function payments()
    {
        return $this->hasMany(PaymentHistory::class, 'account_id');
    }
}
