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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            
            // Common Specifications // တစ်ခုနဲ့တစ်ခု specifications မတူညီတဲ့ product တွေအတွက် table ခွဲသိမ်းမယ်။
            $table->string('brand');
            $table->string('name');
            $table->string('model');
            $table->string('country_of_origin');
            $table->text('website_url')->nullable();
            $table->longText('description')->nullable();

            // Standard Columns
            $table->unsignedBigInteger("status_id")->nullable();
            $table->unsignedBigInteger("user_id")->nullable()->default(1);

            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('unit')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
