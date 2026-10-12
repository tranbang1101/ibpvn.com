<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerConsultation extends Model
{
    protected $table = 'customer_consultations';

    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'ho_ten',
        'so_dien_thoai',
        'email',
        'noi_dung',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
