<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug');
            $table->string('name');
            $table->integer('status');
            $table->boolean('apply')->default(0);
        });

        Schema::create('entities_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file_id');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
            $table->string('origin');
            $table->string('entity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities_files');
        Schema::dropIfExists('files');
    }
}
