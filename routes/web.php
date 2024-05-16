<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('pass/download/{token}', 'App\Http\Controllers\PassController@download')
    ->name('pass.download');

require __DIR__.'/auth.php';
