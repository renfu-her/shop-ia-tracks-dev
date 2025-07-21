<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'type',
        'product_id',
        'sort_order',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // 商品（當 type 為 product 時）
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // 作用域：只顯示啟用的輪播
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 作用域：按類型篩選
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // 作用域：有效的輪播（在時間範圍內）
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('start_at')
              ->orWhere('start_at', '<=', now());
        })->where(function ($q) {
            $q->whereNull('end_at')
              ->orWhere('end_at', '>=', now());
        });
    }

    // 檢查是否在有效時間內
    public function getIsValidAttribute()
    {
        $now = now();
        return (!$this->start_at || $this->start_at <= $now) &&
               (!$this->end_at || $this->end_at >= $now);
    }
}
