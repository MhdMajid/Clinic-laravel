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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('specialty_id')
                    ->nullable()
                    ->constrained('specialties')
                    ->onDelete('cascade')
                    ->onUpdate('cascade')
                    ->index();

            $table->string('name')->nullable();
            $table->tinyInteger('age')->nullable();
            $table->string('phone', 15)->nullable(); 
            $table->string('email')->nullable();
            $table->string('governorate')->nullable();
            $table->string('academic_certificates')->nullable();
            $table->text('experience')->nullable();
            $table->string('hospital')->nullable();
            $table->string('clinic_location')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
