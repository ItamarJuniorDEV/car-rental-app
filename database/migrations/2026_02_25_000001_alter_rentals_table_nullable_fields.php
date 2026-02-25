<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterRentalsTableNullableFields extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE rentals ALTER COLUMN period_actual_end_date DROP NOT NULL');
        DB::statement('ALTER TABLE rentals ALTER COLUMN final_km DROP NOT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE rentals ALTER COLUMN period_actual_end_date SET NOT NULL');
        DB::statement('ALTER TABLE rentals ALTER COLUMN final_km SET NOT NULL');
    }
}
