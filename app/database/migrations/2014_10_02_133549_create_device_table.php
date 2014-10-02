<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 512);
            $table->integer('sessions')->unsigned();
            $table->float('bounce_rate', 6, 2)->unsigned();
            $table->float('conversion_rate', 6, 2)->unsigned();
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

        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign('devices_dataset_id_foreign');
        });
        Schema::drop('devices');
    }
}
