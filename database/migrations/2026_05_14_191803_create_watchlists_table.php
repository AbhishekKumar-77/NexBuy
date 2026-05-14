<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('alert_price', 12, 2)->nullable();
            $table->string('preferred_platform')->nullable();
            $table->timestamps();

            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};
