<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
      Schema::create('bill_details', function (Blueprint $table) {
                $table->id();
                $table->string('bill_no', 50);
                $table->foreign('bill_no')->references('bill_no')->on('bill_master')->onDelete('cascade');
                $table->integer('item_code')->references('bill_no')->on('item')->onDelete('cascade');;
                $table->string('item_name')->references('bill_no')->on('item')->onDelete('cascade');;
                $table->integer('rate')->references('bill_no')->on('item')->onDelete('cascade');;
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
                $table->timestamps();
});


    }


    public function down(): void
    {
        Schema::dropIfExists('_g_r_n__details');
    }
};
