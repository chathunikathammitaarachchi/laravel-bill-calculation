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
        Schema::create('supplier_dues', function (Blueprint $table) {
            $table->id();
             $table->string('supplier_name');
        $table->integer('grn_no');
        $table->date('g_date');
        $table->decimal('tobe_price', 10, 2);
        $table->decimal('supplier_pay', 10, 2);
        $table->decimal('balance', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_dues');
    }
};
