<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $table = 'gio_hang_chi_tiet';

    public $timestamps = false;

    protected $fillable = [
        'cart_id',
        'product_id',
        'so_luong',
        'don_gia',
    ];

    protected function casts(): array
    {
        return [
            'so_luong' => 'integer',
            'don_gia' => 'decimal:2',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
