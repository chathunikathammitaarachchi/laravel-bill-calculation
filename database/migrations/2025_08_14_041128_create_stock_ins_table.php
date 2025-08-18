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
Schema::create('daily_stock_summaries', function (Blueprint $table) {
    $table->id();
    $table->date('transaction_date');
    $table->string('item_code');
    $table->string('item_name');
    $table->string('source')->nullable(); // <-- Add this line
    $table->integer('stock_in')->default(0);
    $table->integer('stock_out')->default(0);
    $table->integer('net_stock')->storedAs('stock_in - stock_out');
    $table->timestamps();

    $table->unique(['transaction_date', 'item_code']);
});

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stock_summaries');
    }
};
