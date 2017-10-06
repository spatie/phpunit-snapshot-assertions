<?php

namespace Spatie\Snapshots\Drivers;

use Exception;
use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;

class FileHashDriver implements Driver
{
    public function serialize($data): string
    {
        if (! file_exists($data)) {
            throw new Exception('File does not exist');
        }

        return sha1_file($data);
    }

    public function extension(): string
    {
        return 'txt';
    }

    public function match($expected, $actual)
    {
        Assert::assertSame($expected, $actual);
    }
}
