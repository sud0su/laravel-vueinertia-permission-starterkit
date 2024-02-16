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
        Schema::connection(config('satusehatsimrs.database_connection'))->create(config('satusehatsimrs.cron_table_name'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rsklien_id');
            $table->string('crontitle');
            $table->string('croncat');
            $table->string('day');
            $table->string('hour');
            $table->string('minute');
            $table->timestamps();

            $table->foreign('rsklien_id')->references('id')->on(config('satusehatsimrs.klien_table_name'))->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('satusehatsimrs.database_connection'))->dropIfExists(config('satusehatsimrs.cron_table_name'));
    }
};
