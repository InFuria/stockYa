<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('company_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('social')->nullable();
            $table->integer('city_id');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->double('score', 3, 2)->nullable();
            $table->double('delivery', 5,2)->nullable();
            $table->string('zone')->nullable();
            $table->integer('status');
            $table->string('attention_hours')->nullable();
            $table->integer('category_id');
            $table->foreign('category_id')->references('id')->on('company_categories')->onDelete('cascade');
            $table->integer('company_id')->nullable();
            $table->integer('visits')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('company_categories');
        Schema::dropIfExists('cities');
    }
}
