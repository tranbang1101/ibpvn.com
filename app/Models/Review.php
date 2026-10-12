<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'danh_gia';

    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'user_id',
        'ten_hien_thi',
        'so_sao',
        'noi_dung',
        'trang_thai',
    ];

    protected function casts(): array
    {
        return [
            'so_sao' => 'integer',
            'trang_thai' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
