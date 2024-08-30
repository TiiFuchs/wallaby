<?php

namespace App\Data\Uic918;

use App\Data\Uic918\FlexibleContent\FlexibleContent;
use App\Data\Uic918\TicketLayout\TicketLayout;
use App\Data\Uic918\VURecord\VURecord;
use App\Exceptions\Uic918\InvalidDataException;
use Spatie\LaravelData\Attributes\MapInputName;

class Ticket extends Record
{
    #[MapInputName('messageTypeId')]
    public string $id;

    #[MapInputName('messageTypeVersion')]
    public string $version;

    public string $companyCode;

    public string $signatureKeyId;

    public string $signature;

    #[MapInputName('compressedMessage')]
    public string $rawMessage;

    public ?MainRecord $mainRecord = null;

    public ?TicketLayout $ticketLayout = null;

    public ?FlexibleContent $flexibleContent = null;

    public ?VURecord $vuRecord = null;

    public function isValid(): bool
    {
        // Read public key
        $filepath = storage_path('app/uic_public_keys/'.$this->companyCode.$this->signatureKeyId.'.pem');
        $certificate = openssl_pkey_get_public(file_get_contents($filepath));

        $algorithm = match ($this->version) {
            '01' => OPENSSL_ALGO_SHA1,
            '02' => OPENSSL_ALGO_SHA256,
            default => throw new InvalidDataException("Invalid messagetype version: {$this->version}"),
        };

        // Verify signature
        $verify = openssl_verify($this->rawMessage, $this->signature, $certificate, $algorithm);

        return $verify === 1;
    }
}
