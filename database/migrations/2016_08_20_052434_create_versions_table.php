<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('repository_id')->unsigned()->index();
            $table->string('name')->index();
            $table->string('latest_release')->index();
            $table->string('sha', 40)->index();
            $table->string('commit_hash')->index();
            $table->timeStamps();

            $table->foreign('repository_id')
                  ->references('id')
                  ->on('repositories')
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
        Schema::drop('versions');
    }
}
