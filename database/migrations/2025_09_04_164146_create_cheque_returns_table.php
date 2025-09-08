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
       Schema::create('cheque_returns', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('supplier_due_payment_id');
    $table->string('reason')->nullable();
    $table->date('return_date');
    $table->timestamps();

    $table->foreign('supplier_due_payment_id')->references('id')->on('supplier_due_payments')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheque_returns');
    }
};
