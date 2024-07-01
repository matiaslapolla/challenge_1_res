<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingController;

Route::apiResource('tours', TourController::class);
Route::apiResource('hotels', HotelController::class);
Route::apiResource('bookings', BookingController::class);

Route::post('bookings/export', [BookingController::class, 'exportAllBookings']);
Route::post('bookings/{id}/cancel', [BookingController::class, 'cancelBooking']);
