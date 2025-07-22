<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MemberCoupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'coupon_code_id',
        'status',
        'used_at',
        'expired_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * 取得會員
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 取得優惠券代碼
     */
    public function couponCode(): BelongsTo
    {
        return $this->belongsTo(CouponCode::class);
    }

    /**
     * 取得優惠券
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id')
                    ->through('couponCode');
    }

    /**
     * 檢查是否可用
     */
    public function isUsable(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expired_at && Carbon::now()->gt($this->expired_at)) {
            return false;
        }

        if (!$this->couponCode || !$this->couponCode->isUsable()) {
            return false;
        }

        return true;
    }

    /**
     * 使用優惠券
     */
    public function use(): bool
    {
        if (!$this->isUsable()) {
            return false;
        }

        $this->update([
            'status' => 'used',
            'used_at' => Carbon::now(),
        ]);

        // 同時更新優惠券代碼狀態
        $this->couponCode->use();

        return true;
    }

    /**
     * 標記為過期
     */
    public function markAsExpired(): bool
    {
        $this->update([
            'status' => 'expired',
            'expired_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * 取得狀態顯示文字
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => '有效',
            'used' => '已使用',
            'expired' => '已過期',
            default => '未知',
        };
    }

    /**
     * 取得折扣金額
     */
    public function getDiscountAmount($totalAmount = 0): float
    {
        if (!$this->couponCode || !$this->couponCode->coupon) {
            return 0;
        }

        return $this->couponCode->coupon->getDiscountAmount($totalAmount);
    }

    /**
     * 檢查是否符合最低消費金額
     */
    public function meetsMinimumAmount($totalAmount): bool
    {
        if (!$this->couponCode || !$this->couponCode->coupon) {
            return false;
        }

        return $this->couponCode->coupon->meetsMinimumAmount($totalAmount);
    }

    /**
     * 作用域：有效的優惠券
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * 作用域：已使用的優惠券
     */
    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    /**
     * 作用域：已過期的優惠券
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * 作用域：可用的優惠券
     */
    public function scopeUsable($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expired_at')
                          ->orWhere('expired_at', '>', Carbon::now());
                    })
                    ->whereHas('couponCode', function ($q) {
                        $q->usable();
                    });
    }
}
