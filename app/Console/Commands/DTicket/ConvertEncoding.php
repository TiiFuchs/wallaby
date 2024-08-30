<?php

namespace App\Console\Commands\DTicket;

use App\Exceptions\Uic918\InvalidDataException;
use App\Models\PassDetails\DTicket;
use App\Services\Uic918Parser\TicketParser;
use Illuminate\Console\Command;

use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class ConvertEncoding extends Command
{
    protected $signature = 'd-ticket:convert-encoding';

    protected $description = 'Converts encoding from utf8 to latin1';

    /** @var array[] */
    protected array $log;

    public function handle(): void
    {
        $parser = new TicketParser;

        progress('Converting barcodes...', DTicket::all(), function (DTicket $ticket) use ($parser) {
            try {
                spin(fn () => $parser->parse($ticket->barcode), 'Checking barcode...');

                $this->log[] = ['note', "{$ticket->id}: No conversion necessary"];
            } catch (InvalidDataException $e) {
                $this->convertEncoding($ticket);
            }
        });

        $this->outputLog();
    }

    protected function convertEncoding(DTicket $ticket): void
    {
        $barcode = mb_convert_encoding($ticket->barcode, 'latin1', 'utf8');

        try {
            spin(fn () => (new TicketParser)->parse($barcode), 'Checking barcode again...');
        } catch (InvalidDataException $e) {
            $this->log[] = ['error', "{$ticket->id}: Encoding conversion failed"];

            return;
        }

        $ticket->update([
            'barcode' => $barcode,
        ]);

        $this->log[] = ['info', "{$ticket->id}: Conversion successfully"];
    }

    protected function outputLog(): void
    {
        foreach ($this->log as [$method, $text]) {
            $method = "\\Laravel\\Prompts\\$method";
            $method($text);
        }
    }
}
