<?php

namespace Spatie\Snapshots\Drivers;

use JetBrains\PhpStorm\ArrayShape;
use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class JsonDriver implements Driver
{
    #[ArrayShape(['flags' => 'int'])]
    public static $config = [
        'flags' => JSON_PRETTY_PRINT,
    ];

    public function serialize($data): string
    {
        if (is_string($data)) {
            $data = json_decode($data);
        }

        if (is_resource($data)) {
            throw new CantBeSerialized('Resources can not be serialized to json');
        }

        return json_encode($data, self::$config['flags'])."\n";
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        if (is_array($actual)) {
            $actual = json_encode($actual, self::$config['flags'])."\n";
        }

        Assert::assertJsonStringEqualsJsonString($expected, $actual);
    }
}
