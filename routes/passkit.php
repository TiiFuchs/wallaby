<?php

use App\Http\Controllers\PassKitWebServiceController;
use Illuminate\Support\Facades\Route;

// Workaround to get a named route
Route::get('/', function () {
    abort(404);
})->name('webServiceURL');

Route::prefix('v1')->controller(PassKitWebServiceController::class)->group(function () {

    //POST request to webServiceURL/version/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier/serialNumber
    Route::post('devices/{deviceLibraryId}/registrations/{passTypeId}/{serialNumber}', 'registerPass');

    // DELETE request to webServiceURL/version/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier/serialNumber
    Route::delete('devices/{deviceLibraryId}/registrations/{passTypeId}/{serialNumber}', 'unregisterPass');

    // GET request to webServiceURL/version/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier?passesUpdatedSince=tag
    Route::get('devices/{deviceLibraryId}/registrations/{passTypeId}', 'listPasses');

    // GET request to webServiceURL/version/passes/passTypeIdentifier/serialNumber
    Route::get('passes/{passTypeId}/{serialNumber}', 'getPass');

    // POST request to webServiceURL/version/log
    Route::post('log', 'logMessage');

});
