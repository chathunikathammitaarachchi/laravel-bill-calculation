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
        Schema::create('grnmaster', function (Blueprint $table) {
            $table->id();
             $table->string('grn_no', 50)->unique();
                $table->date('g_date');
                $table->string('supplier_name')->references('grn_no')->on('supplier')->onDelete('cascade');
                $table->decimal('total_price', 10, 2);
                $table->decimal('tobe_price', 10, 2);
                $table->decimal('total_discount', 10, 2);
                $table->decimal('supplier_pay' , 10, 2);
                $table->decimal('balance', 10, 2);
              
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grnmaster');
    }
};
