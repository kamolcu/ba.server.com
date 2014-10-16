<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinishedDatasetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('finished_dataset', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 255);
			$table->string('start_date', 10); //YYYY-MM-DD
			$table->string('end_date', 10);
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
		Schema::drop('finished_dataset');
	}

}
