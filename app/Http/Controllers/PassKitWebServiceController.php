<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidPassKitAuthTokenException;
use App\Models\Device;
use App\Models\Pass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PassKitWebServiceController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     *
     * @see https://developer.apple.com/documentation/walletpasses/register_a_pass_for_update_notifications
     */
    public function registerPass(Request $request, string $deviceLibraryId, string $passTypeId, string $serialNumber)
    {
        // Get pass
        $pass = Pass::wherePassTypeId($passTypeId)->whereSerialNumber($serialNumber)->first();
        abort_if($pass === null, 404);

        // Check authorization
        try {
            $authorizationHeader = $request->header('Authorization');
            $this->verifyAuthToken($pass, $authorizationHeader);
        } catch (InvalidPassKitAuthTokenException) {
            abort(401);
        }

        // Get pushToken
        $pushToken = $request->input('pushToken');

        // Save device
        $device = Device::updateOrCreate([
            'device_library_identifier' => $deviceLibraryId,
        ], [
            'push_token' => $pushToken,
        ]);

        // Check if pass is already registered with this device
        if ($device->passes()->whereId($pass->id)->exists()) {
            return response(status: 200);
        }

        // Register device
        $pass->devices()->attach($device);

        return response(status: 201);

    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     *
     * @see https://developer.apple.com/documentation/walletpasses/unregister_a_pass_for_update_notifications
     */
    public function unregisterPass(Request $request, string $deviceLibraryId, string $passTypeId, string $serialNumber)
    {
        // Get device
        $device = Device::whereDeviceLibraryIdentifier($deviceLibraryId)->first();
        abort_if($device === null, 404);

        // Get pass
        $pass = Pass::wherePassTypeId($passTypeId)->whereSerialNumber($serialNumber)->first();
        abort_if($pass === null, 404);

        // Verify authToken
        try {
            $authorizationHeader = $request->header('Authorization');
            $this->verifyAuthToken($pass, $authorizationHeader);
        } catch (InvalidPassKitAuthTokenException) {
            abort(401);
        }

        // Abort if pass is not registered to this device
        abort_if(! $device->passes()->whereId($pass->id)->exists(), 404);

        // Remove registration
        $device->passes()->detach($pass);

        // Delete device if there are no more passes registered
        if ($device->passes()->count() === 0) {
            $device->delete();
        }

        return response(status: 200);
    }

    public function listPasses(Request $request, string $deviceLibraryId, string $passTypeId)
    {
        $tag = Carbon::createFromTimestamp($request->query('passesUpdatedSince', 0));

        // Get Device
        $device = Device::whereDeviceLibraryIdentifier($deviceLibraryId)->first();
        abort_if($device === null, 404);

        // Filter for passes that changed since $tag
        $passes = $device->passes()
            ->with('details')
            ->wherePassTypeId($passTypeId)
            ->get()
            ->filter(fn (Pass $pass) => $pass->details->updated_at->greaterThan($tag));

        abort_if($passes->isEmpty(), 204);

        return response()->json([
            'lastUpdated' => $passes->pluck('details.updated_at')->max()->timestamp,
            'serialNumbers' => $passes->pluck('serial_number')->toArray(),
        ]);
    }

    public function getPass(Request $request, string $passTypeId, string $serialNumber)
    {
        $pass = Pass::wherePassTypeId($passTypeId)->whereSerialNumber($serialNumber)->first();
        abort_if($pass === null, 404);

        try {
            $authorizationHeader = $request->header('Authorization');
            $this->verifyAuthToken($pass, $authorizationHeader);
        } catch (InvalidPassKitAuthTokenException) {
            abort(401);
        }

        $pass->update([
            'last_requested_at' => now(),
        ]);

        return response()->streamDownload(fn () => $pass->generate(true));
    }

    public function logMessage(Request $request)
    {
        $log = $request->input('logs');

        foreach ($log as $message) {
            Log::info($message);
        }
    }

    protected function verifyAuthToken(Pass $pass, $authorizationHeader): string
    {
        if (! str_starts_with($authorizationHeader, 'ApplePass ')) {
            throw new InvalidPassKitAuthTokenException('Invalid Format');
        }

        $authToken = Str::after($authorizationHeader, 'ApplePass ');

        // Compare AuthToken with pass related value in database.
        if ($pass->authentication_token !== $authToken) {
            throw new InvalidPassKitAuthTokenException('Authorization is invalid');
        }

        return $authToken;
    }
}
