<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkedVehicle extends Model
{
    protected $fillable = [
        'vehicle_identifier',
        'parked_at',
        'unparked_at',
        'spots_occupied',
    ];
}
