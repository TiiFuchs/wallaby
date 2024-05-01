<?php

namespace App\Http\Controllers;

use App\Models\Pass;

class PassController extends Controller
{
    public function getPass(string $passTypeId, string $serialNumber, string $authenticationToken)
    {
        $pass = Pass::wherePassTypeId($passTypeId)->whereSerialNumber($serialNumber)->first();

        abort_if($pass->authentication_token !== $authenticationToken, 401);

        return response()->streamDownload(fn () => $pass->generate(true));
    }
}
