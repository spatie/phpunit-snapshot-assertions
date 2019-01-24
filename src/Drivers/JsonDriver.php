<?php

namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class JsonDriver implements Driver
{
    public function serialize($data): string
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return json_encode($data, JSON_PRETTY_PRINT).PHP_EOL;
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        Assert::assertJsonStringEqualsJsonString($expected, $actual);
    }
}
