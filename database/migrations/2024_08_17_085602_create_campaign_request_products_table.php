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
        Schema::create('campaign_request_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_request_id')->nullable();
            $table->foreign('campaign_request_id')->references('id')->on('campaign_requests')->onDelete('cascade'); 
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('quantity')->nullable();
            $table->string('product_size')->nullable();
            $table->string('product_color')->nullable();
            $table->double('campaign_price',10,2)->nullable();
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
        Schema::dropIfExists('campaign_request_products');
    }
};
