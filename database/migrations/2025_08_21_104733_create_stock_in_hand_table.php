<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockInHandTable extends Migration
{
    public function up()
    {
        Schema::create('stock_in_hands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_code')->unique();  // assuming item_code is unique
            $table->string('item_name');
            $table->integer('stock_in')->default(0);
            $table->integer('stock_out')->default(0);
            $table->integer('stock_balance')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_in_hands');
    }
}

