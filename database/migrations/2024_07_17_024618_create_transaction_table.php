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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_outpatient');
            $table->date('date');
            $table->string('payment_methode')->nullable();
            $table->bigInteger('total_transaction');
            $table->bigInteger('remaining_payment');
            $table->bigInteger('amount');
            $table->bigInteger('return_amount');
            $table->string('payment_status', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
