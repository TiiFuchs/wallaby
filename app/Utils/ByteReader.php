<?php

namespace App\Utils;

class ByteReader
{
    protected int $position = 0;

    public function __construct(
        protected string $data,
    ) {}

    public function getPosition(): int
    {
        return $this->position;
    }

    public function next(?int $length = null): string
    {
        $bytes = substr($this->data, $this->position, $length);
        $this->skip($length);

        return $bytes;
    }

    public function unpack(string $format): string|array
    {
        $value = unpack($format, $this->data, $this->position);

        preg_match('/([\w@])(\d*|\*)/', $format, $match);
        if ($match[2] === '*') {
            $this->position = strlen($this->data);
        } elseif (is_numeric($match[2])) {
            // Hex gets only a nibble
            if (in_array($match[1], ['h', 'H'])) {
                if ($match[2] % 2 !== 0) {
                    throw new \BadMethodCallException('You cannot unpack an odd number of nibbles using the \'H\' or \'h\' unpack formats with ByteReader::unpack(). Please make sure you request an even number of nibbles.');
                }
                $match[2] = $match[2] / 2;
            }

            $this->skip($match[2]);
        } else {
            $this->skip(match ($format) {
                'q', 'Q', 'J', 'P' => 8,
                'l', 'L', 'N', 'V' => 4,
                's', 'S', 'n', 'v' => 2,
                default => 1,
            });
        }

        if (count($value) > 1) {
            return $value;
        }

        return reset($value);
    }

    public function eof(): bool
    {
        return $this->position >= strlen($this->data);
    }

    public function jump(int $position): void
    {
        $this->position = $position;
    }

    public function skip(int $length): void
    {
        $this->position += $length;
    }

    public function reset(): void
    {
        $this->position = 0;
    }

    public function all(): string
    {
        return $this->data;
    }
}
