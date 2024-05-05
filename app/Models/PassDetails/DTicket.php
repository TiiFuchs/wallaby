<?php

namespace App\Models\PassDetails;

use App\Casts\Base64Cast;
use App\Facades\ZXParser;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DTicket extends PassDetails
{
    use HasFactory;

    protected $table = 'passes_dticket';

    protected $fillable = [
        'name',
        'valid_in',
        'barcode',
        'telegram_user_id',
    ];

    protected function casts(): array
    {
        return [
            'valid_in' => 'datetime',
            'barcode' => Base64Cast::class,
        ];
    }

    public function getPassTypeId(): string
    {
        return 'pass.one.tii.d-ticket';
    }

    public function parseScreenshot(string $filename): void
    {
        $data = ZXParser::parse($filename);

        $this->update([
            'valid_in' => now()->startOfMonth(),
            'barcode' => $data,
        ]);
    }

    public function getJsonData(): array
    {
        $dateString = $this->valid_in !== null
            ? $this->valid_in->startOfMonth()->format('d.m. - ').$this->valid_in->endOfMonth()->format('d.m.Y')
            : '';

        $barcodes = $this->barcode !== null ? [
            [
                'format' => 'PKBarcodeFormatAztec',
                'message' => $this->barcode,
                'messageEncoding' => 'iso-8859-1',
            ],
        ] : [];

        return [
            'description' => 'D-Ticket',
            'organizationName' => 'D-Ticket',
            'sharingProhibited' => true,

            'labelColor' => '#919191',
            'foregroundColor' => '#000000',
            'backgroundColor' => '#ffffff',

            'generic' => [
                'primaryFields' => [
                    [
                        'key' => 'valid_date',
                        'label' => 'Gültigkeitszeitraum',
                        'value' => $dateString,
                        'changeMessage' => 'Dein Deutschlandticket wurde aktualisiert für %@.',
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
                        'textAlignment' => 'PKTextAlignmentRight',
                    ],
                ],
            ],

            'barcodes' => $barcodes,

            'associatedStoreIdentifiers' => [
                343555245,
            ],
        ];
    }
}
