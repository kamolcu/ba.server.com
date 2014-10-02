<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGoalFunnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('goal_funnels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 512);
            $table->integer('sessions')->unsigned();
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
    public function down() {
        Schema::table('goal_funnels', function (Blueprint $table) {
            $table->dropForeign('goal_funnels_dataset_id_foreign');
        });
        Schema::drop('goal_funnels');
    }
}
