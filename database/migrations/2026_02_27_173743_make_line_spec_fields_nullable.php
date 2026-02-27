<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->integer('door_count')->nullable()->change();
            $table->integer('seats')->nullable()->change();
            $table->boolean('air_bag')->nullable()->change();
            $table->boolean('abs')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->integer('door_count')->nullable(false)->change();
            $table->integer('seats')->nullable(false)->change();
            $table->boolean('air_bag')->nullable(false)->change();
            $table->boolean('abs')->nullable(false)->change();
        });
    }
};
