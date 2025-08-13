<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
       Schema::create('bill_master', function (Blueprint $table) {
                $table->id(); 
                $table->string('bill_no', 50)->unique();
                $table->date('grn_date');
                $table->string('customer_name')->references('bill_no')->on('customer')->onDelete('cascade');
                $table->decimal('total_price', 10, 2);
                $table->decimal('tobe_price', 10, 2);
                $table->decimal('total_discount', 10, 2);
                $table->decimal('customer_pay' , 10, 2);
                $table->decimal('balance', 10, 2);
                $table ->string('received_by');
                $table ->string('issued_by');
                $table->timestamps();
});

    }

   
    public function down(): void
    {
        Schema::dropIfExists('_g_r_n__master');
    }
};
