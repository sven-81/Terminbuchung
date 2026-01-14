<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ConsultantController;
use Illuminate\Support\Facades\Route;

Route::get('/consultants', [ConsultantController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);

