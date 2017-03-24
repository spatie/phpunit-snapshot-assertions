<?php

namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;

class VarDriver implements Driver
{
    public function serialize($data): string
    {
        return '<?php return '.var_export($data, true).';'.PHP_EOL;
    }

    public function extension(): string
    {
        return 'php';
    }

    public function load(string $path)
    {
        return include $path;
    }

    public function match($expected, $actual)
    {
        Assert::assertEquals($expected, $actual);
    }
}
