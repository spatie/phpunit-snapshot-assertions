<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\HtmlDriver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class HtmlDriverTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_html_string_to_pretty_html()
    {
        $driver = new HtmlDriver();

        $expected = implode("\n", [
            '<!DOCTYPE html>',
            '<html lang="en">',
            '<head></head>',
            '<body><h1>Hello, world!</h1></body>',
            '</html>',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('<!doctype html><html lang="en"><head></head><body><h1>Hello, world!</h1></body></html>'));
    }

    /** @test */
    public function it_can_only_serialize_strings()
    {
        $driver = new HtmlDriver();

        $this->expectException(CantBeSerialized::class);

        $driver->serialize(['foo' => 'bar']);
    }
}
