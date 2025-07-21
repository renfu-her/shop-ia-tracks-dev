<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'stock',
        'sku',
        'barcode',
        'category_id',
        'specifications',
        'tags',
        'is_featured',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'specifications' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    // 分類
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // 圖片
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    // 主要圖片
    public function primaryImage(): BelongsTo
    {
        return $this->belongsTo(ProductImage::class)->where('is_primary', true);
    }

    // 輪播
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

    // 作用域：只顯示啟用的商品
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 作用域：特色商品
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // 作用域：已發布商品
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    // 取得當前價格（考慮特價）
    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    // 檢查是否有特價
    public function getHasDiscountAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    // 計算折扣百分比
    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }
}
