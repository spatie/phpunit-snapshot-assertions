<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\XmlDriver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class XmlDriverTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_xml_string_to_pretty_xml()
    {
        $driver = new XmlDriver();

        $expected = implode(PHP_EOL, [
            '<?xml version="1.0"?>',
            '<foo>',
            '  <bar>baz</bar>',
            '</foo>',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('<foo><bar>baz</bar></foo>'));
    }

    /** @test */
    public function it_can_only_serialize_strings()
    {
        $driver = new XmlDriver();

        $this->expectException(CantBeSerialized::class);

        $driver->serialize(['foo' => 'bar']);
    }
}
