<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalsTable extends Migration
{
    public function up()
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('car_id');
            $table->dateTime('period_start_date');
            $table->dateTime('period_expected_end_date');
            $table->dateTime('period_actual_end_date')->nullable();
            $table->float('daily_rate', 8, 2);
            $table->integer('initial_km');
            $table->integer('final_km')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('car_id')->references('id')->on('cars');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rentals');
    }
}
