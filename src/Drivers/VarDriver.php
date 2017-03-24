<?php

namespace Spatie\Snapshots\Drivers;

use Spatie\Snapshots\Driver;
use PHPUnit\Framework\Assert;

class VarDriver implements Driver
{
    public function serialize($data): string
    {
        return '<?php return '.var_export($data, true).';'.PHP_EOL;
    }

    public function extension(): string
    {
        return '.php';
    }

    public function load(string $path): string
    {
        return include $path;
    }

    public function match($expected, $actual)
    {
        Assert::assertEquals($expected, $actual);
    }
}
