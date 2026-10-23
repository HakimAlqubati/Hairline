<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HairlineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hairline Beauty Lounge — Customer-Facing App Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HairlineController::class, 'show'])->defaults('initialView', 'home')->name('home');
Route::get('/about', [HairlineController::class, 'show'])->defaults('initialView', 'about')->name('about');
Route::get('/services', [HairlineController::class, 'show'])->defaults('initialView', 'services')->name('services');
Route::get('/booking', [HairlineController::class, 'show'])->defaults('initialView', 'booking')->name('booking');
Route::get('/profile', [HairlineController::class, 'show'])->defaults('initialView', 'account')->name('profile');

Route::get('/booking/availability', [BookingController::class, 'getAvailability'])->name('booking.availability');
Route::post('/booking/verify-phone', [BookingController::class, 'verifyPhone'])->name('booking.verify_phone');
Route::post('/booking/google-register', [BookingController::class, 'registerWithGoogle'])->name('booking.google_register');
Route::get('/auth/google/redirect', [BookingController::class, 'redirectToGoogle'])->name('booking.google_redirect');
Route::get('/auth/google/callback', [BookingController::class, 'handleGoogleCallback'])->name('booking.google_callback');
Route::post('/profile/update', [BookingController::class, 'updateProfile'])->name('profile.update');
