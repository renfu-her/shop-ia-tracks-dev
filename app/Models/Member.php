<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'address',
        'birthday',
        'gender',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'address' => 'array',
        'birthday' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * 取得會員擁有的優惠券代碼
     */
    public function couponCodes(): HasMany
    {
        return $this->hasMany(CouponCode::class);
    }

    /**
     * 取得會員贈送的優惠券代碼
     */
    public function giftedCouponCodes(): HasMany
    {
        return $this->hasMany(CouponCode::class, 'gifted_by_member_id');
    }

    /**
     * 取得會員的優惠券關聯
     */
    public function memberCoupons(): HasMany
    {
        return $this->hasMany(MemberCoupon::class);
    }

    /**
     * 取得會員可用的優惠券
     */
    public function usableCoupons()
    {
        return $this->memberCoupons()->usable();
    }

    /**
     * 取得會員已使用的優惠券
     */
    public function usedCoupons()
    {
        return $this->memberCoupons()->used();
    }

    /**
     * 取得會員已過期的優惠券
     */
    public function expiredCoupons()
    {
        return $this->memberCoupons()->expired();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
}
