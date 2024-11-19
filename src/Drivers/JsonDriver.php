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
            $data = json_decode($data);
        }

        if (is_resource($data)) {
            throw new CantBeSerialized('Resources can not be serialized to json');
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        if (is_string($actual)) {
            $actual = json_decode($actual, false, 512, JSON_THROW_ON_ERROR);
        }
        $expected = json_decode($expected, false, 512, JSON_THROW_ON_ERROR);
        Assert::assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
    }
}
