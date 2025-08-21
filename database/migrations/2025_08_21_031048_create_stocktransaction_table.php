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
        Schema::create('stocktransaction', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('item_code');
        $table->string('item_name');
        $table->enum('transaction_type', ['IN', 'OUT']);
        $table->integer('quantity');
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
        Schema::dropIfExists('stocktransaction');
    }
};
