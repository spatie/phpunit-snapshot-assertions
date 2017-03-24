<?php

namespace Spatie\Snapshots\Drivers;

use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\JsonMatches;
use Spatie\Snapshots\Driver;

class JsonDriver implements Driver
{
    public function serialize($data): string
    {
        if (! is_string($data)) {
            throw new InvalidArgumentException('Only strings can be serialized to json');
        }

        return json_encode(json_decode($data), JSON_PRETTY_PRINT).PHP_EOL;
    }

    public function extension(): string
    {
        return 'json';
    }

    public function load(string $path)
    {
        return file_get_contents($path);
    }

    public function match($expected, $actual)
    {
        Assert::assertJson($expected);
        Assert::assertJson($actual);

        Assert::assertThat($actual, new JsonMatches($expected));
    }
}
