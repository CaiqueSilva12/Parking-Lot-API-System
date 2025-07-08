<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingEvent extends Model
{
    protected $fillable = [
        'vehicle_id',
        'event_type',
        'timestamp',
    ];
    public $timestamps = false;
} 