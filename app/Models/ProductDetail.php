<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDetail extends Model
{
    protected $table = 'productdetail';

    protected $primaryKey = 'product_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'mo_ta_chi_tiet',
        'thong_so_ky_thuat',
        'bao_hanh',
        'phi_giao_hang',
        'giao_hang_mien_phi',
        'thong_tin_uu_dai',
    ];

    protected function casts(): array
    {
        return [
            'thong_so_ky_thuat' => 'array',
            'phi_giao_hang' => 'decimal:2',
            'giao_hang_mien_phi' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
