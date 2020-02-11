<?php

namespace Spatie\Snapshots\Drivers;

use Spatie\Snapshots\Driver;
use PHPUnit\Framework\Assert;

class TextDriver implements Driver
{
    public function serialize($data): string
    {
        return $data;
    }

    public function extension(): string
    {
        return 'txt';
    }

    public function match($expected, $actual)
    {
        Assert::assertEquals($expected, $this->serialize($actual));
    }
}
