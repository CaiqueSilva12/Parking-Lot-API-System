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
use App\Helpers\ApiResponse;

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

    public function park(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'license_plate' => 'required|string|unique:vehicles,license_plate',
            'timestamp' => 'required|date',
        ]);

        if ($validator->fails()) {
            return ['status' => ApiResponse::VALIDATION_ERROR, 'errors' => [$validator->errors()->first()]];
        }

        $licensePlate = $request->input('license_plate');
        $timestamp = Carbon::parse($request->input('timestamp'))->utc();

        if ($this->vehicleRepository->isParked($licensePlate)) {
            return ['status' => ApiResponse::VEHICLE_ALREADY_PARKED];
        }

        $currentCount = $this->vehicleRepository->getParkedCountWithSpacing($this->spacingRule);
        $requiredSpots = $this->spacingRule->getRequiredSpots($timestamp->dayOfWeek);

        if ($currentCount + $requiredSpots > $this->capacity) {
            return ['status' => ApiResponse::NO_SPACE];
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

        return ['status' => ApiResponse::VEHICLE_PARKED];
    }

    public function unpark(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'license_plate' => 'required|string|exists:vehicles,license_plate',
            'timestamp' => 'required|date',
        ]);

        if ($validator->fails()) {
            return ['status' => ApiResponse::VALIDATION_ERROR, 'errors' => [$validator->errors()->first()]];
        }

        $licensePlate = $request->input('license_plate');
        $timestamp = Carbon::parse($request->input('timestamp'))->utc();

        $vehicle = $this->vehicleRepository->findByLicensePlate($licensePlate);
        if (!$vehicle) {
            return ['status' => ApiResponse::VEHICLE_NOT_FOUND];
        }

        DB::transaction(function () use ($vehicle, $timestamp) {
            ParkingEvent::create([
                'vehicle_id' => $vehicle->id,
                'event_type' => 'unpark',
                'timestamp' => $timestamp,
            ]);
            $vehicle->delete();
        });

        return ['status' => ApiResponse::VEHICLE_UNPARKED];
    }

    public function availableSpots(): array
    {
        $currentCount = $this->vehicleRepository->getParkedCountWithSpacing($this->spacingRule);
        $available = $this->capacity - $currentCount;

        return [
            'status' => ApiResponse::AVAILABLE_SPOTS,
            'data' => ['available_spots' => $available],
        ];
    }
}
