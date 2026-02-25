<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_id',
        'plate',
        'available',
        'km',
    ];

    protected $casts = [
        'available' => 'boolean',
    ];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
