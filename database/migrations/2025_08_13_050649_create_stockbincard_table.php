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
  
    Schema::create('stockbincard', function (Blueprint $table) {
        $table->id();
        $table->string('item_code');
        $table->string('item_name');
        $table->enum('transaction_type', ['IN', 'OUT']);
        $table->integer('quantity');
        $table->string('reference_no')->nullable();
        $table->string('source')->nullable();
        $table->date('transaction_date');
        $table->timestamps();
    });
}




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockbincard');
    }
};
