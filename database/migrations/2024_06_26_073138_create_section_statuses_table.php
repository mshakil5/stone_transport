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
        Schema::create('section_statuses', function (Blueprint $table) {
            $table->id();
            $table->boolean('slider')->default(1); 
            $table->boolean('special_offer')->default(1); 
            $table->boolean('campaigns')->default(1); 
            $table->boolean('features')->default(1); 
            $table->boolean('categories')->default(1); 
            $table->boolean('feature_products')->default(1); 
            $table->boolean('flash_sell')->default(1);
            $table->boolean('recent_products')->default(1); 
            $table->boolean('popular_products')->default(1); 
            $table->boolean('trending_products')->default(1); 
            $table->boolean('buy_one_get_one')->default(1); 
            $table->boolean('most_viewed_products')->default(1); 
            $table->boolean('category_products')->default(1); 
            $table->boolean('bundle_products')->default(1); 
            $table->boolean('vendors')->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_statuses');
    }
};
