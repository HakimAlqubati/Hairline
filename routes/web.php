<?php

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
