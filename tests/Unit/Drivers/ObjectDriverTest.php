<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\Drivers\ObjectDriver;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Yaml\Yaml;

class ObjectDriverTest extends TestCase
{
    protected $defaultConfig;

    protected function setUp(): void {
        $this->defaultConfig = ObjectDriver::$config;
    }

    protected function tearDown(): void
    {
        ObjectDriver::$config = $this->defaultConfig;
    }

    /** @test */
    public function it_can_serialize_a_string()
    {
        $driver = new ObjectDriver();

        $this->assertEquals('foo', $driver->serialize('foo'));
    }

    /** @test */
    public function it_can_serialize_an_integer()
    {
        $driver = new ObjectDriver();

        $this->assertEquals('1', $driver->serialize(1));
    }

    /** @test */
    public function it_can_serialize_a_float()
    {
        $driver = new ObjectDriver();

        $this->assertEquals('1.5', $driver->serialize(1.5));
    }

    /** @test */
    public function it_can_serialize_an_associative_array()
    {
        $driver = new ObjectDriver();

        $expected = implode("\n", [
            'foo:',
            '    bar: baz',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(['foo' => ['bar' => 'baz']]));
    }

    /** @test */
    public function it_can_serialize_an_indexed_array_without_keys()
    {
        $driver = new ObjectDriver();

        $expected = implode("\n", [
            '- foo',
            '- bar',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(['foo', 'bar']));
    }

    /** @test */
    public function it_can_serialize_a_simple_object()
    {
        $driver = new ObjectDriver();

        $expected = implode("\n", [
            'foo: bar',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize((object) ['foo' => 'bar']));
    }

    /** @test */
    public function it_can_be_configurable()
    {
        $driver = new ObjectDriver();

        $expected = implode("\n", [
            'foo: bar',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize((object) ['foo' => 'bar']));

        $expected = implode("\n", [
            '  foo: bar',
            '',
        ]);

        ObjectDriver::$config['context']['yaml_indent'] = 2;

        $this->assertEquals($expected, $driver->serialize((object) ['foo' => 'bar']));


        ObjectDriver::$config = [
            'encoder' => JsonEncoder::class,
            'format' => JsonEncoder::FORMAT,
            'context' => []
        ];

        $expected = '{"foo":"bar"}';

        $this->assertEquals($expected, $driver->serialize((object) ['foo' => 'bar']));
    }

    /** @test */
    public function it_can_serialize_a_class_instance()
    {
        $driver = new ObjectDriver();

        $expected = implode("\n", [
            'name: \'My name\'',
            'valid: true',
            'dateTime: \'2020-01-01T15:00:00+01:00\'',
            'public: public',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(new Obj()));
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
