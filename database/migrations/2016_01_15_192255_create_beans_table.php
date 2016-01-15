<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('counter_id')->unsigned()->index();
            $table->integer('value')->index();
            $table->timestamps();

      			$table->foreign('counter_id')
                  ->references('id')
                  ->on('counters')
                  ->onUpdate('cascade')
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
        Schema::drop('beans');
    }
}
