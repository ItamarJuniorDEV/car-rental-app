<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientsTable extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('cpf', 14)->unique()->after('name');
            $table->string('email', 100)->unique()->after('cpf');
            $table->string('phone', 20)->after('email');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['cpf']);
            $table->dropUnique(['email']);
            $table->dropColumn(['cpf', 'email', 'phone']);
        });
    }
}
