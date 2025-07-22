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
        Schema::table('members', function (Blueprint $table) {
            // 台灣地址相關欄位
            $table->string('county')->nullable()->comment('縣市');
            $table->string('district')->nullable()->comment('區');
            $table->string('zipcode')->nullable()->comment('郵遞區號');
            $table->text('address')->nullable()->comment('詳細地址')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['county', 'district', 'zipcode', 'address']);
        });
    }
};
