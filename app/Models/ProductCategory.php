<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // 上層分類
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    // 子分類
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    // 所有子分類（遞迴）
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    // 商品
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // 作用域：只顯示啟用的分類
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 作用域：只顯示頂層分類
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
