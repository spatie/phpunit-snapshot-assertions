<?php

namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class JsonDriver implements Driver
{
    /** @var int @see https://www.php.net/manual/en/json.constants.php */
    private $flags;

    public function __construct($flags = JSON_PRETTY_PRINT)
    {
        $this->flags = $flags;
    }

    public function serialize($data): string
    {
        if (is_string($data)) {
            $data = json_decode($data);
        }

        if (is_resource($data)) {
            throw new CantBeSerialized('Resources can not be serialized to json');
        }

        return json_encode($data, $this->flags)."\n";
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        if (is_array($actual)) {
            $actual = json_encode($actual, JSON_PRETTY_PRINT)."\n";
        }

        Assert::assertJsonStringEqualsJsonString($expected, $actual);
    }
}
