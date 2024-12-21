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
        Schema::create('buy_one_get_one_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buy_one_get_one_id');
            $table->foreign('buy_one_get_one_id')->references('id')->on('buy_one_get_ones')->onDelete('cascade');
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
        Schema::dropIfExists('buy_one_get_one_images');
    }
};
