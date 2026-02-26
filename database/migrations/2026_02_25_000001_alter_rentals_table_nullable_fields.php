<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterRentalsTableNullableFields extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE rentals MODIFY COLUMN period_actual_end_date DATETIME NULL');
            DB::statement('ALTER TABLE rentals MODIFY COLUMN final_km INT NULL');
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE rentals MODIFY COLUMN period_actual_end_date DATETIME NOT NULL');
            DB::statement('ALTER TABLE rentals MODIFY COLUMN final_km INT NOT NULL');
        }
    }
}
