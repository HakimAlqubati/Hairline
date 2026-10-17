<?php

use App\Livewire\Pages\About;
use App\Livewire\Pages\Booking;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Profile;
use App\Livewire\Pages\Services;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hairline Beauty Lounge — Customer-Facing App Routes
|--------------------------------------------------------------------------
*/

Route::get('/', Home::class)->name('home');
Route::get('/about', About::class)->name('about');
Route::get('/services', Services::class)->name('services');
Route::get('/booking', Booking::class)->name('booking');
Route::get('/profile', Profile::class)->name('profile');
