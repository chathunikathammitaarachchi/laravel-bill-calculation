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
        Schema::create('grndetails', function (Blueprint $table) {
            $table->id();
             $table->string('grn_no', 50);
                $table->foreign('grn_no')->references('grn_no')->on('grnmaster')->onDelete('cascade');
                $table->integer('item_code')->references('grn_no')->on('item')->onDelete('cascade');;
                $table->string('item_name')->references('grn_no')->on('item')->onDelete('cascade');;
                $table->integer('rate')->references('grn_no')->on('item')->onDelete('cascade');;
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grndetails');
    }
};
