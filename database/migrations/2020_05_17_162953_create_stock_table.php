<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('stock', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->double('quantity',15,5);
            $table->unsignedInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('stock_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_id');
            $table->foreign('stock_id')->references('id')->on('stock')->onDelete('cascade');
            $table->string('type');
            $table->double('old_quantity', 15, 5);
            $table->double('new_quantity', 15, 5);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('stock_audit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('status');
            $table->timestamps();
        });

        Schema::create('stock_audit_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('audit_id');
            $table->foreign('audit_id')->references('id')->on('stock_audit')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->double('system_declared', 15, 5);
            $table->double('fisic_declared', 15, 5);
            $table->unsignedInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
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
        Schema::dropIfExists('stock_audit_detail');
        Schema::dropIfExists('stock_audit');
        Schema::dropIfExists('stock_records');
        Schema::dropIfExists('stock');
        Schema::dropIfExists('units');
    }
}
