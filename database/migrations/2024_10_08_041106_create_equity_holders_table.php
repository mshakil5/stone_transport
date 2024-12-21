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
        Schema::create('equity_holders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('company_name')->nullable();
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('phone')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('tin')->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->longText('address')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches');
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
        Schema::dropIfExists('equity_holders');
    }
};
