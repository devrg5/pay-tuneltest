<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->string('description', 50);
            $table->string('address');
            $table->unsignedInteger('phone_number');
            $table->string('zip', 50);
            $table->string('email', 50);
            $table->string('url_response');
            $table->string('url_base');
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('country', 100);
            $table->string('client_identifier', 50);
            $table->string('token');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            //$table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
