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
        Schema::create('item_summaries', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('item_code');
    $table->string('item_name');
    $table->integer('quantity');
    $table->decimal('rate', 10, 2);
    $table->decimal('total_price', 10, 2);
    $table->date('grn_date');
     $table->string('bill_no', 50); 

    $table->foreign('bill_no')->references('bill_no')->on('bill_master')->onDelete('cascade');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_summaries');
    }
};
