<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'don_hang';

    public const UPDATED_AT = null;

    protected $fillable = [
        'ma_don',
        'user_id',
        'ten_nguoi_nhan',
        'email',
        'so_dien_thoai',
        'dia_chi',
        'tam_tinh',
        'phi_giao_hang',
        'tong_tien',
        'ghi_chu',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'tam_tinh' => 'decimal:2',
            'phi_giao_hang' => 'decimal:2',
            'tong_tien' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
