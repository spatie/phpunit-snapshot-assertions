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

    public function match($expected, $actual)
    {
        $evaluated = eval(substr($expected, strlen('<?php ')));

        Assert::assertEquals($evaluated, $actual);
    }
}
