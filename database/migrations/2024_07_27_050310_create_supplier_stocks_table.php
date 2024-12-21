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
        Schema::create('supplier_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->longText('description')->nullable();
            $table->string('exp_date')->nullable();
            $table->boolean('status')->default(1); // 1 == New
            $table->boolean('is_approved')->default(0);
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
        Schema::dropIfExists('supplier_stocks');
    }
};
