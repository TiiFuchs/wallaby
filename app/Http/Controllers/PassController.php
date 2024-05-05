<?php

namespace App\Http\Controllers;

use App\Models\Pass;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class PassController extends Controller
{
    public function getPass(string $passTypeId, string $serialNumber, string $authenticationToken)
    {
        $pass = Pass::wherePassTypeId($passTypeId)->whereSerialNumber($serialNumber)->first();

        abort_if(Hash::make($pass->authentication_token) !== $authenticationToken, 401);

        return response()->streamDownload(fn () => $pass->generate(true));
    }

    public function download(string $token)
    {
        $id = Cache::get('pass-download:'.$token);

        $pass = Pass::findOrFail($id);

        return response()->streamDownload(fn () => $pass->generate(true));
    }
}
