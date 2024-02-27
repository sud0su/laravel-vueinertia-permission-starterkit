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
        Schema::connection(config('satusehatsimrs.database_connection'))->create(config('satusehatsimrs.getdata_table_name'), function (Blueprint $table) {
            $table->id();
            $table->string('resourceType');
            $table->string('identifier');
            $table->json('data'); // Add unique constraint
            $table->unsignedBigInteger('cron_id');
            $table->enum('flag', [0, 1, 2, 3])->comment('0 untuk error, 1 untuk belum sync, 2 untuk sudah sync, 3 untuk data sudah ada');
            $table->timestamps();
            $table->foreign('cron_id')->references('id')->on(config('satusehatsimrs.cron_table_name'))->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('satusehatsimrs.database_connection'))->dropIfExists(config('satusehatsimrs.getdata_table_name'));

    }
};
