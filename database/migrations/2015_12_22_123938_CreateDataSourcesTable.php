<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('source_type');
            $table->text('description');
            $table->string('status');
            $table->string('last')->nullable();
            $table->dateTime('last_date')->nullable();
            $table->string('frequency');
            $table->text('endpoint')->nullable();
            $table->string('api_format')->nullable();
            $table->string('data_format');
            $table->string('entity');
            $table->integer('entity_id');
            $table->text('properties')->nullable();
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
        Schema::drop('data_sources');
    }
}
