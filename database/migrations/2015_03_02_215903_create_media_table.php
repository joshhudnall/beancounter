<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media', function(Blueprint $table)
		{
  		$table->engine = "InnoDB";
			$table->increments('id');
			$table->string('type', 20);
			$table->string('server')->default('app');
			$table->string('path');
			$table->string('name')->default('');
			$table->string('ext', 10);
			$table->string('aspect_ratio', 10)->default('source');
			$table->text('dimensions')->nullable();
			$table->string('third_party_type')->nullable();
			$table->string('third_party_id')->nullable();
			$table->string('third_party_thumbnail')->nullable();
			$table->integer('processed')->default(0);
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
		Schema::drop('media');
	}

}
