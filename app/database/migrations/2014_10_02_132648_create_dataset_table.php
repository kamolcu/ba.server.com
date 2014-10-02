<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDatasetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('datasets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 512);
			$table->string('start_date', 10); //YYYY-MM-DD
			$table->string('end_date', 10);
			$table->string('desc', 1024)->nullable();
			$table->integer('compare_dataset_id')->unsigned()->nullable();
			$table->foreign('compare_dataset_id')->references('id')->on('datasets');
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
		Schema::drop('datasets');
	}

}
