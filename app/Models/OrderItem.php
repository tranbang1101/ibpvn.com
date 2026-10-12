<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'don_hang_chi_tiet';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'ten_san_pham',
        'so_luong',
        'don_gia',
        'thanh_tien',
    ];

    protected function casts(): array
    {
        return [
            'so_luong' => 'integer',
            'don_gia' => 'decimal:2',
            'thanh_tien' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
