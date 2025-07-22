<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_amount',
        'discount_type',
        'minimum_amount',
        'max_usage',
        'used_count',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'max_usage' => 'integer',
        'used_count' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * 取得優惠券代碼
     */
    public function couponCodes(): HasMany
    {
        return $this->hasMany(CouponCode::class);
    }

    /**
     * 取得未使用的優惠券代碼
     */
    public function unusedCodes(): HasMany
    {
        return $this->hasMany(CouponCode::class)->where('status', 'unused');
    }

    /**
     * 取得已使用的優惠券代碼
     */
    public function usedCodes(): HasMany
    {
        return $this->hasMany(CouponCode::class)->where('status', 'used');
    }

    /**
     * 檢查優惠券是否有效
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        
        if ($now->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return false;
        }

        if ($this->max_usage > 0 && $this->used_count >= $this->max_usage) {
            return false;
        }

        return true;
    }

    /**
     * 檢查是否為百分比折扣
     */
    public function isPercentageDiscount(): bool
    {
        return $this->discount_type === 'percentage';
    }

    /**
     * 取得折扣金額
     */
    public function getDiscountAmount($totalAmount = 0): float
    {
        if ($this->isPercentageDiscount()) {
            return round($totalAmount * ($this->discount_amount / 100), 2);
        }

        return $this->discount_amount;
    }

    /**
     * 檢查是否符合最低消費金額
     */
    public function meetsMinimumAmount($totalAmount): bool
    {
        return $totalAmount >= $this->minimum_amount;
    }

    /**
     * 作用域：啟用的優惠券
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 作用域：有效的優惠券（在有效期間內）
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                    ->where('start_at', '<=', $now)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_at')
                          ->orWhere('end_at', '>=', $now);
                    });
    }

    /**
     * 作用域：可用的優惠券（未達使用上限）
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->where('max_usage', 0)
              ->orWhereRaw('used_count < max_usage');
        });
    }
}
