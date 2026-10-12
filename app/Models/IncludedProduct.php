<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncludedProduct extends Model
{
    protected $table = 'included';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'ten',
        'anh',
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
