<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\YamlDriver;

class YamlDriverTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_yaml_string()
    {
        $driver = new YamlDriver();

        $yamlString = implode("\n", [
            'foo: bar',
            'baz: qux',
            '',
        ]);

        $this->assertEquals($yamlString, $driver->serialize($yamlString));
    }

    /** @test */
    public function it_can_serialize_a_yaml_array()
    {
        $driver = new YamlDriver();

        $expected = implode("\n", [
            'foo: bar',
            'baz: qux',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize([
            'foo' => 'bar',
            'baz' => 'qux',
        ]));
    }
}
