<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'ten_phien_ban',
        'image_url',
        'sku',
        'gia',
    ];

    protected function casts(): array
    {
        return [
            'gia' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
