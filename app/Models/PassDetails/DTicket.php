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

        $month = (now()->isLastOfMonth())
            ? now()->startOfMonth()->addMonth()
            : now()->startOfMonth();

        $this->update([
            'valid_in' => $month,
            'barcode' => $data,
        ]);
    }

    public function getJsonData(): array
    {
        $dateString = '';
        $validIn = '';

        if ($this->valid_in !== null) {
            $dateString = $this->valid_in->startOfMonth()->format('d.m. - ').$this->valid_in->endOfMonth()->format('d.m.Y');
            $validIn = $this->valid_in->translatedFormat('F Y');
        }

        $barcodes = [];

        if ($this->barcode !== null) {
            $barcodes = [
                [
                    'format' => 'PKBarcodeFormatAztec',
                    'message' => $this->barcode,
                    'messageEncoding' => 'iso-8859-1',
                ],
            ];
        }

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
                'backFields' => [
                    [
                        'key' => 'valid_in',
                        'label' => 'Gültig in',
                        'value' => $validIn,
                        'changeMessage' => 'Dein Deutschlandticket wurde aktualisiert für %@.',
                    ],
                    [
                        'key' => 'telegram_user_id',
                        'label' => 'Telegram User ID',
                        'value' => $this->telegram_user_id,
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
