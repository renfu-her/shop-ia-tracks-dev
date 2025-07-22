<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CouponCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'coupon_id',
        'code',
        'member_id',
        'gifted_by_member_id',
        'status',
        'used_at',
        'gifted_at',
        'accepted_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'gifted_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * 取得對應的優惠券
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * 取得擁有者會員
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 取得贈送者會員
     */
    public function giftedByMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'gifted_by_member_id');
    }

    /**
     * 取得會員優惠券關聯
     */
    public function memberCoupons(): HasMany
    {
        return $this->hasMany(MemberCoupon::class);
    }

    /**
     * 檢查代碼是否可用
     */
    public function isUsable(): bool
    {
        if ($this->status !== 'unused') {
            return false;
        }

        if (!$this->coupon || !$this->coupon->isActive()) {
            return false;
        }

        return true;
    }

    /**
     * 使用優惠券代碼
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

        // 更新優惠券使用次數
        $this->coupon->increment('used_count');

        return true;
    }

    /**
     * 贈送優惠券代碼
     */
    public function gift($giftedByMemberId): bool
    {
        if ($this->status !== 'unused') {
            return false;
        }

        $this->update([
            'status' => 'gifted',
            'gifted_by_member_id' => $giftedByMemberId,
            'gifted_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * 接受贈送的優惠券
     */
    public function accept($memberId): bool
    {
        if ($this->status !== 'gifted') {
            return false;
        }

        $this->update([
            'member_id' => $memberId,
            'accepted_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * 檢查是否已過期
     */
    public function isExpired(): bool
    {
        if (!$this->coupon) {
            return true;
        }

        if ($this->coupon->end_at && Carbon::now()->gt($this->coupon->end_at)) {
            return true;
        }

        return false;
    }

    /**
     * 取得狀態顯示文字
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'unused' => '未使用',
            'used' => '已使用',
            'expired' => '已過期',
            'gifted' => '已贈送',
            default => '未知',
        };
    }

    /**
     * 作用域：未使用的代碼
     */
    public function scopeUnused($query)
    {
        return $query->where('status', 'unused');
    }

    /**
     * 作用域：已使用的代碼
     */
    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    /**
     * 作用域：已贈送的代碼
     */
    public function scopeGifted($query)
    {
        return $query->where('status', 'gifted');
    }

    /**
     * 作用域：可用的代碼
     */
    public function scopeUsable($query)
    {
        return $query->where('status', 'unused')
                    ->whereHas('coupon', function ($q) {
                        $q->active()->valid()->available();
                    });
    }
}
