<?php

use App\Http\Controllers\Api\CourierController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

Route::apiResource('couriers', CourierController::class);
