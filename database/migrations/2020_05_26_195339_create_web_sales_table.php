<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('payment_id');
            $table->foreign('payment_id')->references('id')->on('payment_methods')->onDelete('cascade');
            $table->string('address');
            $table->boolean('delivery')->default(0);
            $table->integer('status')->default(0);
            $table->double('total', 10,2);
            $table->integer('tracker');
            $table->string('tags');
            $table->string('text');
            $table->timestamps();
        });

        Schema::create('web_sale_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('web_sale_id');
            $table->foreign('web_sale_id')->references('id')->on('web_sales')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->double('subtotal', 10,2);
        });

        Schema::create('web_sale_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('web_sales')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('web_transactions');
        Schema::dropIfExists('web_sale_detail');
        Schema::dropIfExists('web_sales');
    }
}
