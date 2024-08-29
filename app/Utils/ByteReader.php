<?php

namespace App\Utils;

class ByteReader
{
    protected int $position = 0;

    public function __construct(
        protected string $data,
    ) {}

    public function next(?int $length = null): string
    {
        $bytes = substr($this->data, $this->position, $length);
        $this->skip($length);

        return $bytes;
    }

    public function unpack(string $format): string|array
    {
        $value = unpack($format, $this->data, $this->position);

        preg_match('/[\w@](\d+|\*)/', $format, $match);
        if ($match[1] === '*') {
            $this->position = strlen($this->data);
        } else {
            $this->skip($match[1]);
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
