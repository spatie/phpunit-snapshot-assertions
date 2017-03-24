<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class JsonDriverTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_json_string_to_pretty_json()
    {
        $driver = new JsonDriver();

        $expected = implode(PHP_EOL, [
           '{',
            '    "foo": "bar"',
            '}',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('{"foo":"bar"}'));
    }

    /** @test */
    public function it_can_only_serialize_strings()
    {
        $driver = new JsonDriver();

        $this->expectException(CantBeSerialized::class);

        $driver->serialize(['foo' => 'bar']);
    }
}
