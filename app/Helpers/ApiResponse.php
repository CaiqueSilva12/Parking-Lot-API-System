<?php

namespace App\Helpers;

class ApiResponse
{
    public const SUCCESS = 'SUCCESS';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const VEHICLE_ALREADY_PARKED = 'VEHICLE_ALREADY_PARKED';
    public const VEHICLE_NOT_FOUND = 'VEHICLE_NOT_FOUND';
    public const NO_SPACE = 'NO_SPACE';
    public const VEHICLE_PARKED = 'VEHICLE_PARKED';
    public const VEHICLE_UNPARKED = 'VEHICLE_UNPARKED';
    public const AVAILABLE_SPOTS = 'AVAILABLE_SPOTS';

    public static function respond(string $code, array $data = [], array $errors = []): \Illuminate\Http\JsonResponse
    {
        $map = [
            self::SUCCESS => ['message' => 'Success.', 'status' => 200],
            self::VALIDATION_ERROR => ['message' => 'Validation failed.', 'status' => 422],
            self::VEHICLE_ALREADY_PARKED => ['message' => 'Vehicle is already parked.', 'status' => 409],
            self::VEHICLE_NOT_FOUND => ['message' => 'Vehicle not found.', 'status' => 404],
            self::NO_SPACE => ['message' => 'No space available.', 'status' => 409],
            self::VEHICLE_PARKED => ['message' => 'Vehicle parked successfully.', 'status' => 201],
            self::VEHICLE_UNPARKED => ['message' => 'Vehicle unparked successfully.', 'status' => 200],
            self::AVAILABLE_SPOTS => ['message' => 'Available spots retrieved.', 'status' => 200],
        ];

        $meta = $map[$code] ?? ['message' => 'Internal server error.', 'status' => 500];

        $response = ['message' => $meta['message']];
        if (!empty($data)) {
            $response['data'] = $data;
        }
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $meta['status']);
    }
}
