<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkingAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_area', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('latitude',10,8);
            $table->decimal('longitude',11,8);
            $table->integer('space');
            $table->integer('space_left');
            $table->integer('free_time');
            $table->unsignedDecimal('weekday_first',8,2)->default(0.00);
            $table->unsignedDecimal('weekday',8,2)->default(0.00);
            $table->unsignedDecimal('weekend_first',8,2)->default(0.00);
            $table->unsignedDecimal('weekend',8,2)->default(0.00);
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
        Schema::dropIfExists('parking_area');
    }
}
