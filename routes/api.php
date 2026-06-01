<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\WorkshopCrudController;
use App\Http\Controllers\Api\Admin\MechanicCrudController;
use App\Http\Controllers\Api\KendaraanController;
use App\Http\Controllers\Api\Admin\OrderCrudController;
use App\Http\Controllers\Api\ChatRoomController;
use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\Admin\UserCrudController;

use App\Http\Middleware;
/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses tanpa login / Tanpa Token)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Webhook harus ditaruh di LUAR middleware auth:sanctum karena dipanggil oleh server Xendit
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);


/*
|--------------------------------------------------------------------------
| Protected Routes (Wajib membawa Bearer Token Sanctum di Postman)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // --- Authentication ---
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Workshops (Bengkel) ---
    Route::get('/workshops', [WorkshopCrudController::class, 'index']);
    Route::get('/workshops/open', [WorkshopCrudController::class, 'open']);
    Route::get('/workshops/top-rated', [WorkshopCrudController::class, 'topRated']);
    Route::get('/workshops/nearest', [WorkshopCrudController::class, 'nearest']);
    Route::get('/workshops/filter', [WorkshopCrudController::class, 'filter']);
    Route::get('/workshops/{id}', [WorkshopCrudController::class, 'show']);
    Route::get('/workshops/{workshopId}/mechanics', [MechanicCrudController::class, 'byWorkshop']);

    // --- Mechanics (Mekanik) ---
    Route::get('/mechanics', [MechanicCrudController::class, 'index']);
    Route::get('/mechanics/nearest', [MechanicCrudController::class, 'nearest']);
    Route::get('/mechanics/{id}/location', [MechanicCrudController::class, 'showLocation']);
    Route::patch('/mechanics/{id}/location', [MechanicCrudController::class, 'updateLocation']);

    // Fitur Tambah Mekanik (Admin Office / Backoffice)
    Route::post('/mechanics/admin-store', [MechanicCrudController::class, 'store']);
// Users Route Api
Route::get('/users', [UserCrudController::class, 'index']);
Route::post('/users', [UserCrudController::class, 'store']);
Route::get('/users/{id}/edit', [UserCrudController::class, 'edit']);
Route::put('/users/{id}', [UserCrudController::class, 'update']);
Route::delete('/users/{id}', [UserCrudController::class, 'destroy']);

    // --- Kendaraan ---
    Route::get('/kendaraan', [KendaraanController::class, 'index']);
    Route::post('/kendaraan', [KendaraanController::class, 'store']);
    Route::get('/kendaraan/{id}', [KendaraanController::class, 'show']);

    // --- Orders & Tracking (Duplikasi Sudah Dihapus) ---
    Route::post('/orders', [OrderCrudController::class, 'store']);
    Route::get('/orders/{id}/tracking', [OrderCrudController::class, 'tracking']);

    // --- Chat Room API ---
    Route::post('/chat-rooms', [ChatRoomController::class, 'store']);
    Route::get('/chat-rooms/{id}', [ChatRoomController::class, 'show']);

    // --- Chat Message API ---
    Route::get('/chat-rooms/{chat_room_id}/messages', [ChatMessageController::class, 'index']);
    Route::post('/chat-messages', [ChatMessageController::class, 'store']);

    // --- Payment API (Hanya Request Invoice yang Butuh Auth) ---
    Route::post('/payments', [PaymentController::class, 'store']);

    // --- Order Item API ---
    Route::get('/orders/{order_id}/items', [OrderItemController::class, 'index']);
    Route::post('/order-items', [OrderItemController::class, 'store']);

    // --- Log API ---
    Route::get('/orders/{order_id}/logs', [LogController::class, 'index']);

    // Route::get('/mechanic/orders', [OrderCrudController::class, 'mechanicOrders'])
//--- order ---
    Route::post('/orders/{id}/accept', [OrderCrudController::class, 'accept']);
    Route::post('/orders/{id}/arrive', [OrderCrudController::class, 'arrive']);
    Route::post('/orders/{id}/complete', [OrderCrudController::class, 'complete']);

    //--- terbaru ---/
    Route::patch('/mechanics/{id}/status',[MechanicCrudController::class, 'updateStatus']);
    Route::get('/mechanic/orders', [OrderCrudController::class, 'mechanicOrders']);

});
