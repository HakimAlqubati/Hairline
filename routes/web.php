<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hairline Beauty Lounge — Customer-Facing App Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('hairline', ['initialView' => 'home']);
})->name('home');

Route::get('/about', function () {
    return view('hairline', ['initialView' => 'about']);
})->name('about');

Route::get('/services', function () {
    return view('hairline', ['initialView' => 'services']);
})->name('services');

Route::get('/booking', function () {
    return view('hairline', ['initialView' => 'booking']);
})->name('booking');

Route::get('/profile', function () {
    return view('hairline', ['initialView' => 'account']);
})->name('profile');
