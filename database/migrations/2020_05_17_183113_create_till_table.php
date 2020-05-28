<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('till', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->boolean('status');
            $table->double('opening_cash', 15, 2);
            $table->double('actual_cash', 15, 2);
        });

        Schema::create('till_audit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('till_id');
            $table->foreign('till_id')->references('id')->on('till')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('register_cash', 15, 2);
            $table->double('declared_cash', 15, 2);
            $table->boolean('status');
            $table->timestamps();
        });

        Schema::create('transaction_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('till_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('till_id');
            $table->foreign('till_id')->references('id')->on('till')->onDelete('cascade');
            $table->unsignedInteger('type_id');
            $table->foreign('type_id')->references('id')->on('transaction_types')->onDelete('cascade');
            $table->integer('detail_id')->nullable();
            $table->double('cash_before', 15, 2);
            $table->double('cash_after', 15, 2);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('till_transactions');
        Schema::dropIfExists('transaction_types');
        Schema::dropIfExists('till_audit');
        Schema::dropIfExists('till');
    }
}
