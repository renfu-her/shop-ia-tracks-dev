<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->comment('會員ID');
            $table->unsignedBigInteger('coupon_code_id')->comment('優惠券代碼ID');
            $table->enum('status', ['active', 'used', 'expired'])->default('active')->comment('狀態：有效/已使用/已過期');
            $table->timestamp('used_at')->nullable()->comment('使用時間');
            $table->timestamp('expired_at')->nullable()->comment('過期時間');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes')->onDelete('cascade');
            
            $table->unique(['member_id', 'coupon_code_id']);
            $table->index(['member_id', 'status']);
            $table->index(['coupon_code_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_coupons');
    }
};
