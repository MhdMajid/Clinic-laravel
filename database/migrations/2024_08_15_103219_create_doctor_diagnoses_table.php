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
        Schema::create("doctor_diagnoses", function (Blueprint $table) 
        {
            $table->id();

            $table->unsignedBigInteger('doctor_id')->nullable()->index();
            $table->unsignedBigInteger('patient_id')->nullable()->index();
            $table->unsignedBigInteger('patient_file_id')->nullable()->index();
            $table->unsignedBigInteger('completed_appointment_id')->nullable()->index();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->string('medical_tests')->nullable();
            $table->string('radiographs')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('doctor_id') 
                  ->references('id')
                  ->on('doctors')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
    
            $table->foreign('patient_id') 
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
          
            $table->foreign('patient_file_id') 
                  ->references('id')
                  ->on('patient_files')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
         
            $table->foreign('completed_appointment_id') 
                  ->references('id')
                  ->on('completed_appointments')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_diagnoses');

    }
};
