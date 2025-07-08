<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\VehicleRepository;
use App\Rules\SpacingRule;
use App\Models\Vehicle;
use App\Models\ParkingEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class ParkingLotService
{
    protected $vehicleRepository;
    protected $spacingRule;
    protected $capacity = 100;

    public function __construct(VehicleRepository $vehicleRepository, SpacingRule $spacingRule)
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->spacingRule = $spacingRule;
    }

    public function park(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_plate' => 'required|string|unique:vehicles,license_plate',
            'timestamp' => 'required|date',
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()], 422);
        }
        $licensePlate = $request->input('license_plate');
        $timestamp = Carbon::parse($request->input('timestamp'))->utc();
        if ($this->vehicleRepository->isParked($licensePlate)) {
            return response(['message' => 'Vehicle is already parked.'], 409);
        }
        $currentCount = $this->vehicleRepository->getParkedCountWithSpacing($this->spacingRule);
        $requiredSpots = $this->spacingRule->getRequiredSpots($timestamp->dayOfWeek);
        if ($currentCount + $requiredSpots > $this->capacity) {
            return response(['message' => 'No space available.'], 409);
        }
        DB::transaction(function () use ($licensePlate, $timestamp) {
            $vehicle = Vehicle::create([
                'license_plate' => $licensePlate,
                'parked_at' => $timestamp,
            ]);
            ParkingEvent::create([
                'vehicle_id' => $vehicle->id,
                'event_type' => 'park',
                'timestamp' => $timestamp,
            ]);
        });
        return response(['message' => 'Vehicle parked successfully.'], 201);
    }

    public function unpark(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_plate' => 'required|string|exists:vehicles,license_plate',
            'timestamp' => 'required|date',
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()], 422);
        }
        $licensePlate = $request->input('license_plate');
        $timestamp = Carbon::parse($request->input('timestamp'))->utc();
        $vehicle = $this->vehicleRepository->findByLicensePlate($licensePlate);
        if (!$vehicle) {
            return response(['message' => 'Vehicle not found.'], 404);
        }
        DB::transaction(function () use ($vehicle, $timestamp) {
            ParkingEvent::create([
                'vehicle_id' => $vehicle->id,
                'event_type' => 'unpark',
                'timestamp' => $timestamp,
            ]);
            $vehicle->delete();
        });
        return response(['message' => 'Vehicle unparked successfully.'], 200);
    }

    public function availableSpots()
    {
        $currentCount = $this->vehicleRepository->getParkedCountWithSpacing($this->spacingRule);
        $available = $this->capacity - $currentCount;
        return response(['available_spots' => $available], 200);
    }
} 