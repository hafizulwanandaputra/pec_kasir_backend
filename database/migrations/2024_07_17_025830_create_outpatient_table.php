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
        Schema::create('outpatients', function (Blueprint $table) {
            $table->id();
            $table->string('no_registration')->unique();
            $table->string('code_of_poli');
            $table->string('code_of_doctor');
            $table->string('no_mr');
            $table->string('code_of_assurance');
            $table->string('poli_name');
            $table->string('doctor_name');
            $table->string('patient_name');
            $table->string('assurance');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outpatients');
    }
};
