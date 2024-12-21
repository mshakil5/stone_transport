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
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('mother_vassels_id')->nullable();
            $table->unsignedBigInteger('lighter_vassels_id')->nullable();
            $table->unsignedBigInteger('ghats_id')->nullable();

            $table->foreign('mother_vassels_id')->references('id')->on('mother_vassels')->onDelete('cascade');
            $table->foreign('lighter_vassels_id')->references('id')->on('lighter_vassels')->onDelete('cascade');
            $table->foreign('ghats_id')->references('id')->on('ghats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            //
        });
    }
};
