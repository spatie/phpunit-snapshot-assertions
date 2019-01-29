<?php

namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;
use Symfony\Component\Yaml\Yaml;

class YamlDriver implements Driver
{
    public function serialize($data): string
    {
        if (is_string($data)) {
            $data = Yaml::parse($data);
        }

        if (! is_array($data)) {
            throw new CantBeSerialized('Only strings can be serialized to json');
        }

        return Yaml::dump($data);
    }

    public function extension(): string
    {
        return 'yml';
    }

    public function match($expected, $actual)
    {
        if (is_array($actual)) {
            $actual = Yaml::dump($actual);
        }

        Assert::assertEquals($expected, $actual);
    }
}
