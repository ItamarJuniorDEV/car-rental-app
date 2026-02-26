<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinesTable extends Migration
{
    public function up()
    {
        Schema::create('lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_id')->constrained('brands');

            $table->string('name', 30);
            $table->string('image', 100);
            $table->integer('door_count');
            $table->integer('seats');
            $table->boolean('air_bag');
            $table->boolean('abs');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lines');
    }
}
