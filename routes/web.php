<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('pass/{passTypeId}/{serialNumber}/{authenticationToken}', 'App\Http\Controllers\PassController@getPass');
