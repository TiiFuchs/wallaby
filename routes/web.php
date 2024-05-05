<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('pass/download/{token}', 'App\Http\Controllers\PassController@download')
    ->name('pass.download');
