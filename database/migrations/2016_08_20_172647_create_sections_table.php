<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chapter_id')->unsigned();
            $table->string('name');
            $table->integer('number')->unsigned()->index();
            $table->string('path');
            $table->timeStamps();

            $table->foreign('chapter_id')
                  ->references('id')
                  ->on('chapters')
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
        Schema::drop('sections');
    }
}
