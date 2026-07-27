<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fruit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'unit',
        'current_stock',
        'minimum_stock',
        'purchase_price',
        'selling_price',
        'supplier',
        'storage_location',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(InventoryAlert::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return 'Kadaluarsa';
        }

        if ((float) $this->current_stock <= (float) $this->minimum_stock) {
            return 'Stok rendah';
        }

        if ($this->expiry_date && $this->expiry_date->between(now()->startOfDay(), now()->addDays(3)->endOfDay())) {
            return 'Segera habis masa simpan';
        }

        return 'Aman';
    }
}
