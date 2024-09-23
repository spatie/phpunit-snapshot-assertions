<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\YamlDriver;

class YamlDriverTest extends TestCase
{
    #[Test]
    public function it_can_serialize_a_yaml_string()
    {
        $driver = new YamlDriver;

        $yamlString = implode("\n", [
            'foo: bar',
            'baz: qux',
            '',
        ]);

        $this->assertEquals($yamlString, $driver->serialize($yamlString));
    }

    #[Test]
    public function it_can_serialize_a_yaml_array()
    {
        $driver = new YamlDriver;

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
