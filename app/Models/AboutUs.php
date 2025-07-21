<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUs extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'image',
        'contact_info',
        'social_media',
        'is_active',
    ];

    protected $casts = [
        'contact_info' => 'array',
        'social_media' => 'array',
        'is_active' => 'boolean',
    ];

    // 作用域：只顯示啟用的關於我們
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
