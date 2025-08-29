<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_due_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_due_id');
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->date('cheque_date')->nullable();
            $table->timestamps();

            $table->foreign('supplier_due_id')
                  ->references('id')
                  ->on('supplier_dues')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_due_payments');
    }
};
