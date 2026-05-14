<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('platform'); // gem, amazon, flipkart, indiamart
            $table->decimal('price', 12, 2);
            $table->date('recorded_date');
            $table->timestamps();

            $table->index(['product_id', 'platform', 'recorded_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};
