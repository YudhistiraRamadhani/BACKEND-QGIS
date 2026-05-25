<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WorkshopController;
use App\Http\Controllers\Api\MechanicController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/workshops', [WorkshopController::class, 'index']);
Route::get('/workshops/open', [WorkshopController::class, 'open']);
Route::get('/workshops/top-rated', [WorkshopController::class, 'topRated']);
Route::get('/workshops/nearest', [WorkshopController::class, 'nearest']);
Route::get('/workshops/filter', [WorkshopController::class, 'filter']);
Route::get('/workshops/{workshopId}/mechanics', [MechanicController::class, 'byWorkshop']);
Route::post('/mechanics', [\App\Http\Controllers\Admin\MechanicCrudController::class, 'store']);
Route::get('/mechanics', [MechanicController::class, 'index']);
Route::get('/mechanics/nearest', [MechanicController::class, 'nearest']);
Route::get('/mechanics/{id}/location', [MechanicController::class, 'showLocation']);
Route::patch('/mechanics/{id}/location', [MechanicController::class, 'updateLocation']);

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}/tracking', [OrderController::class, 'tracking']);

});
