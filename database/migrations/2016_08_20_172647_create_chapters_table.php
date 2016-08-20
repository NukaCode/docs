<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('version_id')->unsigned();
            $table->string('name');
            $table->integer('number')->unsigned()->index();
            $table->string('path');
            $table->timeStamps();

            $table->foreign('version_id')
                  ->references('id')
                  ->on('versions')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chapters');
    }
}
