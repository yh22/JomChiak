<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkingCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_code', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parking_id');
            $table->string('code');
            $table->timestamps();
            $table->foreign('parking_id')->references('id')->on('parking_area');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parking_code');
    }
}
