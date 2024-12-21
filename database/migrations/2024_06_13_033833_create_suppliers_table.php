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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->string('balance')->default(0);
            $table->string('vat_reg')->nullable();
            $table->string('address')->nullable(); 
            $table->string('company')->nullable(); 
            $table->string('contract_date')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
