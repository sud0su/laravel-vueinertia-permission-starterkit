<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return voidÏ
     */
    public function up()
    {
        Schema::connection(config('satusehatsimrs.database_connection'))->create(config('satusehatsimrs.token_table_name'), function (Blueprint $table) {
            $table->string('environment');
            $table->longText('token');
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
        Schema::connection(config('satusehatsimrs.database_connection'))->dropIfExists(config('satusehatsimrs.token_table_name'));
    }
};
