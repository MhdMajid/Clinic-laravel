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
        Schema::create('patients_health_information', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('patient_id')->nullable()->index();
            $table->unsignedBigInteger('doctor_id')->nullable()->index();
            $table->string('chronic_diseases')->nullable();
            $table->string('previous_surgeries')->nullable();
            $table->string('permanent_medications')->nullable();
            $table->string('current_disease_symptoms')->nullable();
            $table->string('visit_type')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id') 
                    ->references('id')
                    ->on('patients')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
      
            $table->foreign('doctor_id') 
                    ->references('id')
                    ->on('doctors')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients_health_information');
    }
};
