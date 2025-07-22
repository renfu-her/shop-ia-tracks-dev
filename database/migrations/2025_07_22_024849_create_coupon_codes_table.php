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
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id')->comment('優惠券ID');
            $table->string('code')->unique()->comment('優惠券代碼');
            $table->unsignedBigInteger('member_id')->nullable()->comment('擁有者會員ID');
            $table->unsignedBigInteger('gifted_by_member_id')->nullable()->comment('贈送者會員ID');
            $table->enum('status', ['unused', 'used', 'expired', 'gifted'])->default('unused')->comment('狀態：未使用/已使用/已過期/已贈送');
            $table->timestamp('used_at')->nullable()->comment('使用時間');
            $table->timestamp('gifted_at')->nullable()->comment('贈送時間');
            $table->timestamp('accepted_at')->nullable()->comment('接受時間');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('set null');
            $table->foreign('gifted_by_member_id')->references('id')->on('members')->onDelete('set null');
            
            $table->index(['coupon_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_codes');
    }
};
