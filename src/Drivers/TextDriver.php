<?php

namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;

class TextDriver implements Driver
{
    public function serialize($data): string
    {
        // Normalize line endings for cross-platform tests.
        if (PHP_OS_FAMILY === 'Windows') {
            $data = implode("\n", explode("\r\n", $data));
        }

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
