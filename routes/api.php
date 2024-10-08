<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/vehiculos/registrar', [VehiculoController::class, 'registrar']);
Route::post('/vehiculos/puede-circular', [VehiculoController::class, 'puedeCircular']);
