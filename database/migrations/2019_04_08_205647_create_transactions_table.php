<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id');
            $table->string('cardholder_id', 50);
            $table->string('purchase_operation_number', 20)->unique();
            $table->text('purchase_verification')->nullable();
            $table->string('purchase_amount');
            $table->string('currency_code', 3)->nullable()->default('');
            $table->string('language', 2)->nullable()->default('');
            $table->text('description_products');
            $table->integer('state')->default(2);
            $table->string('cardholder_address')->nullable()->default('');
            $table->string('cardholder_zip', 50)->nullable()->default('');
            $table->string('cardholder_city', 100)->nullable()->default('');
            $table->string('cardholder_state', 100)->nullable()->default('');
            $table->string('cardholder_country', 100)->nullable()->default('');
            $table->string('authorization_code')->nullable()->default('');
            $table->string('authorization_result')->nullable()->default('');
            $table->string('error_code')->nullable()->default('');
            $table->string('error_message')->nullable()->default('');
            $table->string('message_to_client')->nullable()->default('');
            $table->string('phone')->nullable()->default('');
            $table->timestamps();

            $table->foreign('client_id')->references('user_id')->on('clients');
            $table->foreign('cardholder_id')->references('email')->on('cardholders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}