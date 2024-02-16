<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('satusehatsimrs.token_table_name'), function (Blueprint $table) {
            $table->unsignedBigInteger('rsklien_id');
            $table->foreign('rsklien_id')->references('id')->on(config('satusehatsimrs.klien_table_name'))->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('satusehatsimrs.token_table_name'), function (Blueprint $table) {
        });
    }
};
