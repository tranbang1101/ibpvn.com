<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $table = 'product_images';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_url',
        'alt_text',
        'thu_tu',
    ];

    protected function casts(): array
    {
        return [
            'thu_tu' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
