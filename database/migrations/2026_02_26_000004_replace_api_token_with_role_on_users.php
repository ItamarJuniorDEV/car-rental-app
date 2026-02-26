<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['api_token']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('api_token');
            $table->string('role')->default('operador')->after('password');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->string('api_token', 80)->nullable()->unique()->after('password');
        });
    }
};
