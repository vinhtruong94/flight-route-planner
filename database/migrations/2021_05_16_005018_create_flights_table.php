<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('number', 5);
            $table->string('departure_time', 5);
            $table->string('arrival_time', 5);
            $table->float('price', 8, 2);
            $table->unsignedBigInteger('airline_id');
            $table->foreign('airline_id')->references('id')->on('airlines');
            $table->unsignedBigInteger('departure_id');
            $table->foreign('departure_id')->references('id')->on('airports');
            $table->unsignedBigInteger('arrival_id');
            $table->foreign('arrival_id')->references('id')->on('airports');
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
        Schema::dropIfExists('flights');
    }
}
