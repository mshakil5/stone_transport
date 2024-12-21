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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->unsignedBigInteger('chart_of_account_id')->nullable();
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
            $table->string('tran_id')->nullable();

            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            
            $table->string('supplier_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();

            $table->string('table_type')->nullable();
            $table->string('ref')->nullable();
            $table->string('authoriser')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0)->nullable();
            $table->decimal('tax_rate', 10, 2)->default(0)->nullable();
            $table->decimal('tax_amount', 10, 2)->default(0)->nullable();
            $table->decimal('vat_rate', 10, 2)->default(0)->nullable();
            $table->decimal('vat_amount', 10, 2)->default(0)->nullable();
            $table->double('discount', 10, 2)->default(0);
            $table->decimal('at_amount', 10, 2)->default(0)->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('liability_id')->nullable();
            $table->string('share_holder_id')->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->string('payment_type')->nullable();
            $table->string('asset_id')->nullable();
            $table->string('liablity_id')->nullable();
            $table->string('income_id')->nullable();
            $table->string('expense_id')->nullable();
            $table->string('equity_id')->nullable();
            $table->string('document')->nullable();
            $table->longtext('note')->nullable();
            $table->boolean('status')->default(0); // 0 = Approved, 1 = Declined, 2= Pending
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('created_ip')->nullable();
            $table->string('updated_ip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
