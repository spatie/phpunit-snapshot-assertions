<?php

namespace Spatie\Snapshots\Drivers;

use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Spatie\Pixelmatch\Exceptions\CouldNotCompare;
use Spatie\Pixelmatch\Pixelmatch;
use Spatie\Snapshots\Driver;

class ImageDriver implements Driver
{
    public function __construct(
        protected float $threshold = 0.1,
        protected bool $includeAa = true,
    ) {}

    public function serialize($data): string
    {
        return file_get_contents($data);
    }

    public function extension(): string
    {
        return 'png';
    }

    public function match($expected, $actual)
    {
        if (! class_exists(Pixelmatch::class)) {
            throw new Exception('The spatie/pixelmatch package is not installed. Please install it to enable image comparison.');
        }

        $tempPath = sys_get_temp_dir();

        $expectedTempPath = $tempPath.'/expected.png';
        file_put_contents($expectedTempPath, $expected);

        $pixelMatch = Pixelmatch::new($expectedTempPath, $actual)
            ->threshold($this->threshold)
            ->includeAa($this->includeAa);

        try {
            $result = $pixelMatch->matches();
        } catch (CouldNotCompare $exception) {
            throw new ExpectationFailedException($exception->getMessage());
        }

        Assert::assertTrue($result);
    }
}
