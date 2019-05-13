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

        if (! is_array($data)) {
            throw new CantBeSerialized('Only strings can be serialized to json');
        }

        return json_encode($data, JSON_PRETTY_PRINT).PHP_EOL;
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        if (is_array($actual)) {
            $actual = json_encode($actual, JSON_PRETTY_PRINT).PHP_EOL;
        }

        Assert::assertJsonStringEqualsJsonString($expected, $actual);
    }
}
