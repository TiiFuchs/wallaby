<?php

namespace App\Models\PassDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DTicket extends PassDetails
{
    use HasFactory;

    protected $table = 'passes_dticket';

    protected function casts(): array
    {
        return [
            'valid_in' => 'datetime',
        ];
    }

    public function getJsonData(): array
    {
        $dateString =
            $this->valid_in->startOfMonth()->format('d.m. - ').
            $this->valid_in->endOfMonth()->format('d.m.Y');

        return [
            'description' => 'D-Ticket',
            'organizationName' => 'D-Ticket',

            'labelColor' => '#919191',
            'foregroundColor' => '#000000',
            'backgroundColor' => '#ffffff',

            'generic' => [
                'primaryFields' => [
                    [
                        'key' => 'valid_date',
                        'label' => 'Gültigkeitszeitraum',
                        'value' => $dateString,
                    ],
                ],
                'secondaryFields' => [
                    [
                        'key' => 'name',
                        'label' => 'Name',
                        'value' => $this->name,
                    ],
                ],
                'auxiliaryFields' => [
                    [
                        'key' => 'class',
                        'label' => 'Klasse',
                        'value' => '2. Klasse',
                    ],
                    [
                        'key' => 'scope',
                        'label' => 'Geltungsbereich',
                        'value' => 'Bundesweit',
                    ],
                ],
            ],

            'barcodes' => [
                [
                    'altText' => 'ABCDEF',
                    'format' => 'PKBarcodeFormatAztec',
                    'message' => $this->barcode,
                    'messageEncoding' => 'utf-8',
                ],
            ],

            'associatedStoreIdentifiers' => [
                343555245,
            ],
        ];
    }
}
