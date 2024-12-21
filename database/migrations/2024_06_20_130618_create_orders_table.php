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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('invoice')->nullable();
            $table->string('purchase_date')->nullable();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('town')->nullable();
            $table->string('postcode')->nullable();
            $table->longText('note')->nullable();
            $table->integer('vat_percent')->default(0);
            $table->decimal('vat_amount', 10, 2)->nullable();
            $table->decimal('subtotal_amount', 10, 2)->nullable();
            $table->decimal('shipping_amount', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('ref')->nullable();
            $table->string('remarks')->nullable();
            $table->boolean('order_type')->default(0);  //0==ecommerce, 1==in_house, 2==quotation
            $table->boolean('status')->default(1);  //1== pending, 2==processing, 3==packed, 4==shipped, 5==delivered 6==returned, 7==cancelled
            $table->boolean('due_status')->default(0); //0==full_paid, 1==due_pending
            $table->unsignedBigInteger('delivery_man_id')->nullable();
            $table->foreign('delivery_man_id')->references('id')->on('delivery_men');
            $table->boolean('admin_notify')->default(0);
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
        Schema::dropIfExists('orders');
    }
};
