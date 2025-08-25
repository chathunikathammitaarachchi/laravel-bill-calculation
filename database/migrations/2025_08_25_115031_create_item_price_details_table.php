<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_price_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('item')->onDelete('cascade');
            $table->integer('rate');
            $table->integer('cost_price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_price_details');
    }
};
