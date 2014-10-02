<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompleteOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('complete_orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 512);
            $table->integer('count')->unsigned();
            $table->integer('dataset_id')->unsigned()->nullable();
            $table->foreign('dataset_id')->references('id')->on('datasets');
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
		Schema::table('complete_orders', function (Blueprint $table) {
            $table->dropForeign('complete_orders_dataset_id_foreign');
        });
		Schema::drop('complete_orders');
	}

}
