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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade'); 
            $table->string('purchase_date')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('ref')->nullable();
            $table->string('vat_reg')->nullable();
            $table->longText('remarks')->nullable();
            $table->decimal('total_amount',10,2)->nullable();
            $table->decimal('discount',10,2)->nullable();
            $table->decimal('total_vat_amount',10,2)->nullable();
            $table->decimal('net_amount',10,2)->nullable();
            $table->decimal('paid_amount',10,2)->nullable();
            $table->decimal('due_amount',10,2)->nullable();

            $table->decimal('direct_cost',10,2)->nullable();
            $table->decimal('cost_a',10,2)->nullable();
            $table->decimal('cost_b',10,2)->nullable();
            $table->decimal('cnf_cost',10,2)->nullable();
            $table->decimal('other_cost',10,2)->nullable();

            $table->boolean('status')->default(1);
            // 1==Processing, 2==On The Way, 3==Customs, 4==Received
            $table->string('payment_status')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
