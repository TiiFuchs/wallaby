<?php

namespace App\Services;

use App\Data\Geocoding\Address;
use Illuminate\Support\Facades\Http;

class GoogleGeocodingService
{
    const GEOCODE_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function data(string $address): ?Address
    {
        $response = Http::withQueryParameters([
            'key' => config('services.google.api_key'),
            'address' => $address,
            'language' => 'de',
            'region' => 'de',
        ])->get(self::GEOCODE_API_URL);

        $results = $response->json('results');

        if (count($results) === 0 || $results[0]['geometry']['location_type'] !== 'ROOFTOP') {
            return null;
        }

        return Address::from([
            'address' => $results[0]['formatted_address'],
            'coordinates' => [
                'latitude' => $results[0]['geometry']['location']['lat'],
                'longitude' => $results[0]['geometry']['location']['lng'],
            ],
        ]);
    }
}
