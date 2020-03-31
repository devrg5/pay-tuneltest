<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardHoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cardholders', function (Blueprint $table) {
            $table->string('email', 50)->primary();
            $table->string('user_commerce', 10);
            $table->string('name', 30);
            $table->string('last_name', 50);
            $table->string('user_code_payme', 30)->nullable();
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
        Schema::dropIfExists('card_holders');
    }
}
