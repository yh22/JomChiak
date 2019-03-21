<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('parking_id');
            $table->bigInteger('enter');
            $table->bigInteger('exit')->nullable($value = true);
            $table->boolean('confirm');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users_info');
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
        Schema::dropIfExists('track');
    }
}
