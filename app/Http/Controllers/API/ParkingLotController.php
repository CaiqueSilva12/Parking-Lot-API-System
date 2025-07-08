<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ParkingLotService;
use App\Helpers\ApiResponse;

class ParkingLotController extends Controller
{
    protected $parkingLotService;

    public function __construct(ParkingLotService $parkingLotService)
    {
        $this->parkingLotService = $parkingLotService;
    }

    public function park(Request $request)
    {
        $result = $this->parkingLotService->park($request);
        return ApiResponse::respond($result['status'], $result['data'] ?? [], $result['errors'] ?? []);
    }

    public function unpark(Request $request)
    {
        $result = $this->parkingLotService->unpark($request);
        return ApiResponse::respond($result['status'], $result['data'] ?? [], $result['errors'] ?? []);
    }

    public function availableSpots()
    {
        $result = $this->parkingLotService->availableSpots();
        return ApiResponse::respond($result['status'], $result['data'] ?? []);
    }
}
