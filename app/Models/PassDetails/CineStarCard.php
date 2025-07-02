<?php

namespace App\Models\PassDetails;

use App\Exceptions\CineStarCard\CinemaCoordinateUpdateException;
use App\Exceptions\CineStarCard\InvalidAuthenticationException;
use App\Models\CineStarCard\Cinema;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CineStarCard extends PassDetails
{
    use HasFactory;

    protected $table = 'passes_cinestarcard';

    protected $fillable = [
        // 'name',
        'customer_number',
        'premium_points',
        'regular_cinema_id',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
        ];
    }

    public function regularCinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class);
    }

    public function premiumPoints(): Attribute
    {
        return Attribute::get(fn ($value) => floor($value));
    }

    /**
     * @throws InvalidAuthenticationException
     * @throws GuzzleException
     */
    public function fetchData(): void
    {
        $data = \App\Facades\CineStarCard::username($this->username)
            ->password($this->password)
            ->data();

        $this->customer_number = $data->customerNumber;
        $this->premium_points = $data->premiumPoints;

        // Check for cinema data
        if (! $this->regularCinema) {
            $cinema = Cinema::firstOrCreate([
                'name' => $data->regularCinema,
            ]);

            try {
                $cinema->updateCoordinates();
            } catch (CinemaCoordinateUpdateException $e) {
                //
            }

            $this->regularCinema()->associate($cinema);
        }

        $this->save();
    }

    public function getJsonData(): array
    {
        return [
            'description' => 'CineStar Card',
            'organizationName' => 'CineStar',

            'foregroundColor' => 'rgb(255, 255, 255)',
            'backgroundColor' => 'rgb(27, 32, 41)',
            'labelColor' => 'rgb(255, 255, 255)',

            'storeCard' => [
                'headerFields' => [
                    [
                        'key' => 'bonuspoints',
                        'label' => 'PRÄMIENPUNKTE',
                        'value' => $this->premium_points,
                        'numberStyle' => 'PKNumberStyleDecimal',
                        'changeMessage' => 'Du hast jetzt %@ Prämienpunkte.',
                    ],
                ],

                'auxiliaryFields' => [
                    // [
                    //     'key' => 'name',
                    //     'label' => 'NAME',
                    //     'value' => $this->name,
                    // ],
                ],

                'backFields' => [
                    [
                        'key' => 'regular_cinema',
                        'label' => 'Mein CineStar Stammkino',
                        'value' => $this->regularCinema->name,
                    ],
                ],
            ],

            'locations' => [
                [
                    'latitude' => $this->regularCinema->latitude,
                    'longitude' => $this->regularCinema->longitude,
                    'relevantText' => 'Zeig deine CineStar Card',
                ],
            ],

            'barcodes' => [
                [
                    'format' => 'PKBarcodeFormatPDF417',
                    'message' => $this->customer_number,
                    'altText' => $this->customer_number,
                    'messageEncoding' => 'iso-8859-1',
                ],
            ],

        ];
    }

    public function getPassTypeId(): string
    {
        return 'pass.one.tii.cinestar-card';
    }
}
