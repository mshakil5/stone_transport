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
        Schema::create('purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade'); 
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); 
            $table->string('quantity')->nullable();
            $table->string('product_size')->nullable();
            $table->string('product_color')->nullable();
            $table->double('purchase_price',10,2)->nullable();
            $table->string('vat_percent')->nullable();
            $table->double('vat_amount_per_unit',10,2)->nullable();
            $table->double('total_vat',10,2)->nullable();
            $table->double('total_amount',10,2)->nullable();
            $table->double('total_amount_with_vat',10,2)->nullable();
            $table->string('exp_date')->nullable();
            $table->string('transferred_product_quantity')->nullable();
            $table->string('remaining_product_quantity')->nullable();
            $table->string('missing_product_quantity')->nullable();
            $table->boolean('status')->default(1);
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_histories');
    }
};
