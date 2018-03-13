<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\PlaintextDriver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class PlaintextDriverTest extends TestCase
{
    /** @test */
    public function it_can_only_serialize_strings()
    {
        $driver = new PlaintextDriver();

        $this->expectException(CantBeSerialized::class);

        $driver->serialize(['foo' => 'bar']);
    }
}
