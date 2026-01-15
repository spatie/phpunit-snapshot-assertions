<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\ObjectDriver;

class ObjectDriverTest extends TestCase
{
    /** @test */
    #[Test]
    public function it_can_serialize_a_string()
    {
        $driver = new ObjectDriver;

        $this->assertEquals('foo', $driver->serialize('foo'));
    }

    /** @test */
    #[Test]
    public function it_can_serialize_an_integer()
    {
        $driver = new ObjectDriver;

        $this->assertEquals('1', $driver->serialize(1));
    }

    /** @test */
    #[Test]
    public function it_can_serialize_a_float()
    {
        $driver = new ObjectDriver;

        $this->assertEquals('1.5', $driver->serialize(1.5));
    }

    /** @test */
    #[Test]
    public function it_can_serialize_an_associative_array()
    {
        $driver = new ObjectDriver;

        $expected = implode("\n", [
            'foo:',
            '    bar: baz',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(['foo' => ['bar' => 'baz']]));
    }

    /** @test */
    #[Test]
    public function it_can_serialize_an_indexed_array_without_keys()
    {
        $driver = new ObjectDriver;

        $expected = implode("\n", [
            '- foo',
            '- bar',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(['foo', 'bar']));
    }

    /** @test */
    #[Test]
    public function it_can_serialize_a_simple_object()
    {
        $driver = new ObjectDriver;

        $expected = implode("\n", [
            'foo: bar',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize((object) ['foo' => 'bar']));
    }

    /** @test */
    #[Test]
    public function it_can_serialize_a_class_instance()
    {
        $driver = new ObjectDriver;

        $expected = implode("\n", [
            'name: \'My name\'',
            'valid: true',
            'dateTime: \'2020-01-01T15:00:00+01:00\'',
            'public: public',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(new Obj));
    }

    /** @test */
    #[Test]
    public function it_can_serialize_with_custom_parameters()
    {
        $driver = new ObjectDriver([
            'yaml_inline' => 3,
        ]);

        $nestedObject = (object) [
            'foo' => (object) [
                'bar' => (object) [
                    'baz' => ['qux', 'quux'],
                ],
            ],
        ];
        $expected = implode("\n", [
            'foo:',
            '    bar:',
            '        baz: [qux, quux]',
            '',
        ]);
        $this->assertEquals($expected, $driver->serialize($nestedObject));
    }
}

class Obj
{
    private $private = 'private';

    public $public = 'public';

    public function getName()
    {
        return 'My name';
    }

    public function isValid()
    {
        return true;
    }

    public function getDateTime()
    {
        return new \DateTimeImmutable('2020-01-01 15:00:00+01:00');
    }
}
