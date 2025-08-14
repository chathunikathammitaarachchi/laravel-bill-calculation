<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('stock_transactions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('item_code');
        $table->string('item_name');
        $table->string('transaction_type'); 
        $table->integer('quantity');
        $table->decimal('rate', 10, 2);
        $table->decimal('price', 10, 2);
        $table->string('reference_no'); 
        $table->string('source'); 
        $table->date('transaction_date');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
