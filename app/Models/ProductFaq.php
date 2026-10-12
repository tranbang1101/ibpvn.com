<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFaq extends Model
{
    protected $table = 'product_faq';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'cau_hoi',
        'cau_tra_loi',
        'thu_tu',
        'hien_thi',
    ];

    protected function casts(): array
    {
        return [
            'thu_tu' => 'integer',
            'hien_thi' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
