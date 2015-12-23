<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFetchTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fetch_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active');
            $table->text('endpoint');
            $table->string('entity');
            $table->integer('entity_id');
            $table->string('datasource');
            $table->integer('datasource_id');
            $table->string('fetcher', 100); // Name of fetcher to use to get data
            $table->string('frequency', 32); // OnDemand or every Hour, Day, Week
            $table->integer('count')->default(0); // Every how-many hours, days, months, years. 0 only for on-demand
            $table->dateTime('next')->nullable(); // When to retrive next
            $table->text('properties')->nullable(); // JSON key-value bag
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
        Schema::drop('fetch_tasks');
    }
}
