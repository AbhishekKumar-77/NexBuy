<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('category');
            $table->string('subcategory')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();

            // GeM fields
            $table->decimal('gem_price', 12, 2)->nullable();
            $table->string('gem_product_id')->nullable();
            $table->string('gem_seller')->nullable();
            $table->boolean('gem_bis_certified')->default(false);
            $table->boolean('gem_make_in_india')->default(false);
            $table->boolean('gem_msme_seller')->default(false);
            $table->integer('gem_delivery_days')->nullable();
            $table->integer('gem_warranty_months')->nullable();
            $table->string('gem_seller_rating')->nullable();
            $table->integer('gem_stock')->nullable();

            // Amazon fields
            $table->decimal('amazon_price', 12, 2)->nullable();
            $table->string('amazon_product_id')->nullable();
            $table->string('amazon_seller')->nullable();
            $table->decimal('amazon_rating', 3, 1)->nullable();
            $table->integer('amazon_reviews')->nullable();
            $table->integer('amazon_delivery_days')->nullable();
            $table->integer('amazon_warranty_months')->nullable();
            $table->boolean('amazon_bis_certified')->default(false);
            $table->decimal('amazon_shipping', 10, 2)->default(0);

            // Flipkart fields
            $table->decimal('flipkart_price', 12, 2)->nullable();
            $table->string('flipkart_product_id')->nullable();
            $table->string('flipkart_seller')->nullable();
            $table->decimal('flipkart_rating', 3, 1)->nullable();
            $table->integer('flipkart_reviews')->nullable();
            $table->integer('flipkart_delivery_days')->nullable();
            $table->integer('flipkart_warranty_months')->nullable();
            $table->boolean('flipkart_bis_certified')->default(false);
            $table->decimal('flipkart_shipping', 10, 2)->default(0);

            // IndiaMART fields
            $table->decimal('indiamart_price', 12, 2)->nullable();
            $table->string('indiamart_seller')->nullable();
            $table->integer('indiamart_moq')->default(1); // Minimum order quantity
            $table->integer('indiamart_delivery_days')->nullable();

            // Specs
            $table->json('specifications')->nullable();
            $table->string('unit')->default('piece');
            $table->decimal('gst_percent', 5, 2)->default(18);
            $table->string('gem_premium_score')->nullable(); // 0-100
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
