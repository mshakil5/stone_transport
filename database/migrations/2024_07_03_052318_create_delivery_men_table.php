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
        Schema::create('delivery_men', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('is_type')->default(0);
            /* Delivery Men: 0=>Individual, 1=>Company */
            $table->string('phone')->nullable();
            $table->longText('address')->nullable();
            $table->string('image')->nullable();
            $table->string('nid')->nullable();
            $table->string('nid_image')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('town')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('photo')->nullable();
            $table->longText('about')->nullable();
            $table->string('whatsapp')->nullable();
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
        Schema::dropIfExists('delivery_men');
    }
};
