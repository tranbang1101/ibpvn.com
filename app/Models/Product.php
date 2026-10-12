<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $table = 'product';

    protected $fillable = [
        'slug',
        'ten',
        'danh_muc',
        'thuong_hieu',
        'gia',
        'gia_khuyen_mai',
        'mo_ta_ngan',
        'anh_chinh',
        'luot_xem',
        'rating',
        'so_danh_gia',
        'luot_yeu_thich',
        'so_luong_ton',
        'da_ban',
        'hien_thi',
    ];

    protected function casts(): array
    {
        return [
            'gia' => 'decimal:2',
            'gia_khuyen_mai' => 'decimal:2',
            'rating' => 'decimal:1',
            'luot_xem' => 'integer',
            'so_danh_gia' => 'integer',
            'luot_yeu_thich' => 'integer',
            'so_luong_ton' => 'integer',
            'da_ban' => 'integer',
            'hien_thi' => 'boolean',
        ];
    }

    public function detail(): HasOne
    {
        return $this->hasOne(ProductDetail::class, 'product_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function includedItems(): HasMany
    {
        return $this->hasMany(IncludedProduct::class, 'product_id');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class, 'product_id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class, 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(CustomerConsultation::class, 'product_id');
    }
}
