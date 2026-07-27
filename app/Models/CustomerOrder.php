<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'status',
        'payment_status',
        'payment_provider',
        'payment_token',
        'payment_redirect_url',
        'payment_type',
        'payment_transaction_id',
        'paid_at',
        'payment_payload',
        'subtotal',
        'delivery_fee',
        'total',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_payload' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }
}
