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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('優惠券名稱');
            $table->string('code')->unique()->comment('優惠券代號');
            $table->text('description')->nullable()->comment('優惠券描述');
            $table->decimal('discount_amount', 10, 2)->comment('折扣金額');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed')->comment('折扣類型：固定金額/百分比');
            $table->decimal('minimum_amount', 10, 2)->default(0)->comment('最低消費金額');
            $table->integer('max_usage')->default(0)->comment('最大使用次數，0表示無限制');
            $table->integer('used_count')->default(0)->comment('已使用次數');
            $table->timestamp('start_at')->comment('開始時間');
            $table->timestamp('end_at')->nullable()->comment('結束時間');
            $table->boolean('is_active')->default(true)->comment('啟用狀態');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'start_at', 'end_at']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
