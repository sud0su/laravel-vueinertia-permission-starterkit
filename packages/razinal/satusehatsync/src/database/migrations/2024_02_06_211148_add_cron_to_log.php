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
        Schema::table(config('satusehatsimrs.log_table_name'), function (Blueprint $table) {
            $table->unsignedBigInteger('cron_id');
            $table->foreign('cron_id')->references('id')->on('rs_klien_crontask')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log', function (Blueprint $table) {
            //
        });
    }
};
