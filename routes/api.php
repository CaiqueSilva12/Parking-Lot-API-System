<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ParkingLotController;

Route::post('/park', [ParkingLotController::class, 'park']);
Route::post('/unpark', [ParkingLotController::class, 'unpark']);
Route::get('/available-spots', [ParkingLotController::class, 'availableSpots']); 