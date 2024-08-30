<?php

namespace App\Data\Uic918;

use App\Data\Uic918\FlexibleContent\DocumentDataTicket\OpenTicketData;
use App\Data\Uic918\FlexibleContent\FlexibleContent;
use App\Data\Uic918\TicketLayout\TicketLayout;
use App\Data\Uic918\VURecord\VURecord;
use App\Exceptions\Uic918\InvalidDataException;
use Carbon\CarbonImmutable;
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

    public function tariffDesc(): ?string
    {
        $openTicketData = $this->flexibleContent?->transportDocument->first()->ticket;
        $tariff = $openTicketData instanceof OpenTicketData ? $openTicketData->tariffs->first() : null;

        return $tariff?->tariffDesc ?? $this->ticketLayout->tariffDesc();
    }

    public function name(): string
    {
        $traveler = $this->flexibleContent?->travelerDetail->traveler->first();

        return $traveler ? "{$traveler->firstName} {$traveler->lastName}" : $this->ticketLayout->name();
    }

    public function class(): int
    {
        $openTicketData = $this->flexibleContent?->transportDocument->first()->ticket;
        $classCode = $openTicketData instanceof OpenTicketData ? $openTicketData->classCode : null;

        $class = match ($classCode) {
            'first' => 1,
            'second' => 2,
            default => null,
        };

        return $class ?? $this->ticketLayout->class();
    }

    public function validFrom(): CarbonImmutable
    {
        return $this->ticketLayout->validFrom();
    }

    public function validUntil(): CarbonImmutable
    {
        return $this->ticketLayout->validUntil();
    }
}
