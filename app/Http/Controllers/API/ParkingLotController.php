<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ParkingLotService;

class ParkingLotController extends Controller
{
    protected $parkingLotService;

    public function __construct(ParkingLotService $parkingLotService)
    {
        $this->parkingLotService = $parkingLotService;
    }

    public function park(Request $request)
    {
        return $this->parkingLotService->park($request);
    }

    public function unpark(Request $request)
    {
        return $this->parkingLotService->unpark($request);
    }

    public function availableSpots()
    {
        return $this->parkingLotService->availableSpots();
    }
} 