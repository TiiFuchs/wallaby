<?php

namespace App\Services;

use App\Exceptions\ApplePushServiceException;
use App\Models\Device;
use App\Models\Pass;

class ApplePushService
{
    const PUSH_DEVELOPMENT_SERVER = 'https://api.sandbox.push.apple.com/';

    const PUSH_PRODUCTION_SERVER = 'https://api.push.apple.com/';

    public static function endpoint(): string
    {
        return static::PUSH_PRODUCTION_SERVER;
    }

    public function sendPass(Pass $pass, Device $device): void
    {
        $ch = curl_init(static::endpoint()."3/device/{$device->push_token}");

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

        curl_setopt($ch, CURLOPT_SSLCERT, $pass->getCertificatePath());
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, config('passkit.certificate_password'));
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apns-topic: '.$pass->pass_type_id,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, ! app()->environment('production'));

        $response = curl_exec($ch);

        if ($response === false) {
            throw new ApplePushServiceException('Curl error: '.curl_error($ch));
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($status !== 200) {
            $data = json_decode($response);

            if ($data === null) {
                throw new ApplePushServiceException('Service error: Unknown', $status);
            }

            throw new ApplePushServiceException('Service error: '.$data->reason, $status);
        }
    }
}
