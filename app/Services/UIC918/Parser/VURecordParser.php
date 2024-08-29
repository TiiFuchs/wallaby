<?php

namespace App\Services\UIC918\Parser;

use App\Data\UIC918\VURecord\Efs;
use App\Data\UIC918\VURecord\ValidityArea;
use App\Data\UIC918\VURecord\VURecord;
use App\Exceptions\UIC918\InvalidDataException;
use App\Utils\ByteReader;
use Carbon\CarbonImmutable;

class VURecordParser extends Parser
{
    public function parse(string $rawData): VURecord
    {
        $data = new ByteReader($rawData);

        $record = new VURecord;

        $record->terminalNr = $this->uInt16($data);
        $record->samNr = $this->uInt24($data);
        $record->anzahlPersonen = $this->uInt8($data);

        $efsLength = $this->uInt8($data);

        for ($efsNum = 0; $efsNum < $efsLength; $efsNum++) {

            $record->efsListe[] = $efs = new Efs;

            $efs->berechtigungsNr = $this->uInt32($data);
            $efs->kvpOrgId = $this->uInt16($data);

            $efs->produktNr = $this->uInt16($data);
            $efs->pvOrgId = $this->uInt16($data);
            $efs->gueltigAb = $this->dateTimeCompact($data);
            $efs->gueltigBis = $this->dateTimeCompact($data);
            $efs->preis = (float) $this->uInt24($data) / 100;
            $efs->sequenzNr = $this->uInt32($data);

            $listLength = $this->uInt8($data);

            while ($listLength > 0) {
                $tag = $this->uInt8($data); // 0xDC

                if ($tag !== 0xDC) {
                    throw new InvalidDataException('Invalid Flächenlisten-Tag in 0080VU data.');
                }

                $elementLength = $this->uInt8($data);
                $type = $this->uInt8($data);
                $orgId = $this->uInt16($data);

                $listLength -= 5; // 5 byte for $tag, $elementLength, $type and $orgId
                $elementLength -= 3; // 3 byte for $type and $orgId

                // Workaround to read a flexible elementLength as integer:
                $tp1 = hexdec($data->unpack('H'.($elementLength * 2))); // Multiplied by 2, because H1 equals 4 bit: 0xf = 0b1111
                $listLength -= $elementLength;

                $efs->validAreas[] = new ValidityArea(
                    type: $type,
                    kvpOrgId: $orgId,
                    tp1: $tp1,
                );
            }

        }

        return $record;
    }

    protected function uInt8(ByteReader $data): int
    {
        return $data->unpack('C');
    }

    protected function uInt16(ByteReader $data): int
    {
        return $data->unpack('n');
    }

    protected function uInt24(ByteReader $data): int
    {
        $pack = $data->unpack('C3');

        // Big Endian
        return ($pack[1] << 16) | ($pack[2] << 8) | $pack[3];
    }

    protected function uInt32(ByteReader $data): int
    {
        return $data->unpack('N');
    }

    protected function dateTimeCompact(ByteReader $data): CarbonImmutable
    {
        $binary = str_pad(decbin($this->uInt32($data)), 32, '0', STR_PAD_LEFT);

        // Year 7 bit (added to 1990)
        $year = bindec(substr($binary, 0, 7)) + 1990;
        // Month 4 bit
        $month = bindec(substr($binary, 7, 4));
        // Day 5 bit
        $day = bindec(substr($binary, 11, 5));
        // Hour 5 bit
        $hour = bindec(substr($binary, 16, 5));
        // Minute 6 bit
        $minute = bindec(substr($binary, 21, 6));
        // Second 5 bit
        $second = bindec(substr($binary, 27, 5));

        return CarbonImmutable::create($year, $month, $day, $hour, $minute, $second);
    }
}
