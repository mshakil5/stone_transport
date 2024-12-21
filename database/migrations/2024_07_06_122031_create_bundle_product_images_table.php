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
        Schema::create('bundle_product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bundle_product_id');
            $table->foreign('bundle_product_id')->references('id')->on('bundle_products')->onDelete('cascade');
            $table->string('image');
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
        Schema::dropIfExists('bundle_product_images');
    }
};
