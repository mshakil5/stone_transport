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
        Schema::create('flash_sell_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flash_sell_id')->nullable();
            $table->foreign('flash_sell_id')->references('id')->on('flash_sells')->onDelete('cascade'); 
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('quantity')->nullable();
            $table->decimal('old_price', 8, 2)->nullable();
            $table->decimal('flash_sell_price', 8, 2)->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('flash_sell_details');
    }
};
