<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'car_id',
        'period_start_date',
        'period_expected_end_date',
        'period_actual_end_date',
        'daily_rate',
        'initial_km',
        'final_km',
    ];

    protected $casts = [
        'period_start_date'        => 'datetime',
        'period_expected_end_date' => 'datetime',
        'period_actual_end_date'   => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
