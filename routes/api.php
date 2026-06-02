<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function(){

Route::get('/users', [UserController::class, 'index']);

Route::get('/rooms', [RoomController::class, 'index']);
Route::post('/room', [RoomController::class, 'store']);

Route::get('/bookings', [BookingController::class, 'index']);
Route::post('/booking', [BookingController::class, 'store']);
Route::get('/booking/{id}', [BookingController::class, 'show']);
Route::delete('/booking/{id}', [BookingController::class, 'destroy']);

});


