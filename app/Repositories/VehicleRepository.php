<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Carbon\Carbon;

class VehicleRepository
{
    public function isParked($licensePlate)
    {
        return Vehicle::where('license_plate', $licensePlate)->exists();
    }

    public function findByLicensePlate($licensePlate)
    {
        return Vehicle::where('license_plate', $licensePlate)->first();
    }

    public function getParkedCountWithSpacing($spacingRule)
    {
        $vehicles = Vehicle::all();
        $count = 0;
        foreach ($vehicles as $vehicle) {
            $count += $spacingRule->getRequiredSpots(
                Carbon::parse($vehicle->parked_at)->dayOfWeek
            );
        }
        return $count;
    }
} 