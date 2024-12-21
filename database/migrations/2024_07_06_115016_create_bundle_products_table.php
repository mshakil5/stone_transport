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
        Schema::create('bundle_products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->json('product_ids')->nullable();
            $table->Text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->decimal('total_price', 8, 2)->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->string('feature_image')->nullable();
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
        Schema::dropIfExists('bundle_products');
    }
};
