<?php

namespace App\Services\UIC918\Parser;

use App\Data\UIC918\Ticket;
use App\Exceptions\UIC918\InvalidDataException;
use App\Utils\ByteReader;

use function App\asn1_das_signature;

class TicketParser extends Parser
{
    /**
     * @throws InvalidDataException
     */
    public function parse(string $rawData): Ticket
    {
        $data = new ByteReader($rawData);
        $container = $this->parseContainer($data);

        $ticket = Ticket::from($container);

        $message = new ByteReader($container['message']);
        while (! $message->eof()) {

            $type = $message->next(6); // i.e. U_HEAD, U_TLAY, U_FLEX, 0080VU
            $version = $message->next(2); // i.e. 01
            $length = $message->next(4);

            if (! is_numeric($length)) {
                throw new InvalidDataException('Length of message is not numeric');
            }

            // $length contains $type, $version and $length itself
            $data = $message->next((int) $length - 12);

            // TODO: Call corresponding parser
            match ($type) {
                'U_HEAD' => $ticket->mainRecord = (new MainRecordParser($version))->parse($data),
                'U_TLAY' => true,
                'U_FLEX' => $ticket->flexibleContent = (new FlexibleContentParser($version))->parse($data),
                '0080VU' => true,
                default => false,
            };

        }

        return $ticket;
    }

    /**
     * @return array{
     *     messageTypeId: string,
     *     messageTypeVersion: string,
     *     companyCode: string,
     *     signatureKeyId: string,
     *     signature: string,
     *     compressedMessage: string,
     *     message: string
     * }
     *
     * @throws InvalidDataException
     */
    protected function parseContainer(ByteReader $data)
    {
        $messageTypeId = $data->next(3); // #UT

        if ($messageTypeId !== '#UT') {
            throw new InvalidDataException('Data does not have the correct Messagetype ID (#UT)');
        }

        $messageTypeVersion = $data->next(2); // 01 or 02

        $companyCode = $data->next(4); // i.e. 1080

        $signatureKeyId = $data->next(5); // i.e. 00003

        $signature = $this->signature($messageTypeVersion, $data);

        $length = $data->next(4);

        if (! is_numeric($length)) {
            throw new InvalidDataException('Length of compressed message is not numeric');
        }

        $compressedMessage = $data->next((int) $length);

        $message = zlib_decode($compressedMessage);

        if ($message === false) {
            throw new InvalidDataException('Could not inflate compressed message');
        }

        return compact('messageTypeId', 'messageTypeVersion', 'companyCode', 'signatureKeyId', 'signature', 'compressedMessage', 'message');
    }

    /**
     * @throws InvalidDataException
     */
    protected function signature(string $version, ByteReader $data): string
    {
        return match ($version) {
            '01' => $data->unpack('A50'), // Already in ASN.1 DER format
            '02' => asn1_das_signature($data->unpack('A32'), $data->unpack('A32')), // pure integers, need to be converted to ASN.1 DER format
            default => throw new InvalidDataException("Unknown Messagetype version: {$version}"),
        };
    }
}
