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
            $table->string('coupon_name')->nullable();
            $table->boolean('coupon_type')->default(1); // 1 == Fixed Amount, 2 == Percentage
            $table->decimal('coupon_value', 10, 2)->nullable();
            $table->boolean('status')->default(1);
            $table->integer('times_used')->default(0);
            $table->integer('max_use_per_user')->default(1);
            $table->integer('total_max_use')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
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
