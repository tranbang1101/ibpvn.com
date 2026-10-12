<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAddon extends Model
{
    protected $table = 'product_addons';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'ten',
        'image_url',
        'gia_goc',
        'gia_khuyen_mai',
        'thu_tu',
        'hien_thi',
    ];

    protected function casts(): array
    {
        return [
            'gia_goc' => 'decimal:2',
            'gia_khuyen_mai' => 'decimal:2',
            'thu_tu' => 'integer',
            'hien_thi' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
