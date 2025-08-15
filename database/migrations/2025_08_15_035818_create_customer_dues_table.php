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
    Schema::create('customer_dues', function (Blueprint $table) {
        $table->id();
        $table->string('customer_name');
        $table->integer('bill_no');
        $table->date('grn_date');
        $table->decimal('tobe_price', 10, 2);
        $table->decimal('customer_pay', 10, 2);
        $table->decimal('balance', 10, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_dues');
    }
};
