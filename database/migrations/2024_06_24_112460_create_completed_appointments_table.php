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
        Schema::create('completed_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id')->nullable()->index();
            $table->unsignedBigInteger('doctor_id')->nullable()->index();
            $table->unsignedBigInteger('patient_id')->nullable()->index();
            $table->unsignedBigInteger('patient_health_information_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('appointment_id') 
                  ->references('id')
                  ->on('appointments')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
           
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

            $table->foreign('patient_health_information_id') 
                  ->references('id')
                  ->on('patients_health_information')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completed_appointments');
    }
};
