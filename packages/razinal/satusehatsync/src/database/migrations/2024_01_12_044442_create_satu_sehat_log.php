<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('satusehatsimrs.database_connection'))->create(config('satusehatsimrs.log_table_name'), function (Blueprint $table) {
            $table->string('response_id')->nullable();
            $table->string('action');
            $table->string('url');
            $table->json('payload')->nullable();
            $table->json('response');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('satusehatsimrs.database_connection'))->dropIfExists(config('satusehatsimrs.log_table_name'));
    }
};
