<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // 商品
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // 作用域：主要圖片
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // 作用域：按排序
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
