<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNAWebSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('na_web_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('address');
            $table->boolean('delivery')->default(0);
            $table->integer('status')->default(0);
            $table->double('total', 10,2);
            $table->string('tracker')->nullable();
            $table->string('tags')->nullable();
            $table->string('text')->nullable();
            $table->timestamps();
        });

        Schema::create('na_web_sale_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('na_web_sale_id');
            $table->foreign('na_web_sale_id')->references('id')->on('na_web_sales')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->double('subtotal', 10,2);
        });

        Schema::create('na_web_sale_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('na_web_sales')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('na_web_sale_records');
        Schema::dropIfExists('na_web_sale_detail');
        Schema::dropIfExists('na_web_sales');
    }
}
