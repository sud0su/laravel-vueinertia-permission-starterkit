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
        Schema::connection(config('satusehatsimrs.database_connection'))->create(config('satusehatsimrs.patient_table_name'), function (Blueprint $table) {
            $table->id();
            $table->string('resourceType');
            $table->json('identifier')->unique(); // Add unique constraint
            $table->boolean('active');
            $table->json('name');
            $table->json('telecom');
            $table->string('gender');
            $table->date('birthDate');
            $table->boolean('deceasedBoolean')->nullable();
            $table->json('address');
            $table->json('maritalStatus');
            $table->integer('multipleBirthInteger')->nullable();
            $table->json('contact');
            $table->json('communication');
            $table->json('extension');
            $table->enum('flag', [0, 1, 2, 3])->comment('0 untuk error, 1 untuk belum sync, 2 untuk sudah sync, 3 untuk data sudah ada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('satusehatsimrs.database_connection'))->dropIfExists(config('satusehatsimrs.patient_table_name'));
    }
};
