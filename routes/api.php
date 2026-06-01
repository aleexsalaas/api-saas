<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/users', [UserController::class, 'index']);

Route::get('/rooms', [RoomController::class, 'index']);
Route::post('/room', [RoomController::Class, 'store']);
