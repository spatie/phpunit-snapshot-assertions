<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class JsonDriverTest extends TestCase
{
    protected $defaultConfig;

    protected function setUp(): void {
        $this->defaultConfig = JsonDriver::$config;
    }

    protected function tearDown(): void
    {
        JsonDriver::$config = $this->defaultConfig;
    }

    /** @test */
    public function it_can_serialize_a_json_string_to_pretty_json()
    {
        $driver = new JsonDriver();

        $expected = implode("\n", [
            '{',
            '    "foo": "bar"',
            '}',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('{"foo":"bar"}'));
    }

    /** @test */
    public function it_can_be_configurable()
    {
        $driver = new JsonDriver();

        $expected = implode("\n", [
            '{',
            '    "foo": "bar",',
            '    "\u044e\u043d\u0438\u043a\u043e\u0434": "\u0442\u0435\u0441\u0442"',
            '}',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('{"foo":"bar", "юникод":"тест"}'));

        JsonDriver::$config['flags'] = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;

        $expected = implode("\n", [
            '{',
            '    "foo": "bar",',
            '    "юникод": "тест"',
            '}',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('{"foo":"bar", "юникод":"тест"}'));
    }

    /** @test */
    public function it_can_serialize_a_json_hash_to_pretty_json()
    {
        $driver = new JsonDriver();

        $expected = implode("\n", [
            '{',
            '    "foo": "FOO",',
            '    "bar": "BAR",',
            '    "baz": "BAZ"',
            '}',
            '',
        ]);
        $this->assertEquals($expected, $driver->serialize([
            'foo' => 'FOO',
            'bar' => 'BAR',
            'baz' => 'BAZ',
        ]));

        $expected = implode("\n", [
            '{',
            '    "foo": "FOO",',
            '    "bar": "BAR",',
            '    "baz": {',
            '        "aaa": "AAA",',
            '        "bbb": "BBB",',
            '        "ccc": [',
            '            "xxx",',
            '            "yyy",',
            '            "zzz"',
            '        ]',
            '    }',
            '}',
            '',
        ]);
        $this->assertEquals($expected, $driver->serialize([
            'foo' => 'FOO',
            'bar' => 'BAR',
            'baz' => [
                'aaa' => 'AAA',
                'bbb' => 'BBB',
                'ccc' => ['xxx', 'yyy', 'zzz'],
            ],
        ]));
    }

    /** @test */
    public function it_can_serialize_a_json_array_to_pretty_json()
    {
        $driver = new JsonDriver();

        $expected = implode("\n", [
            '[',
            '    "foo",',
            '    "bar",',
            '    "baz"',
            ']',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(['foo', 'bar', 'baz']));
    }

    /** @test */
    public function it_can_serialize_a_empty_json_object_to_pretty_json()
    {
        $driver = new JsonDriver();

        $expected = implode("\n", [
            '{',
            '    "foo": {',
            '        "bar": true',
            '    },',
            '    "baz": {}',
            '}',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize((object) [
            'foo' => (object) [
                'bar' => true,
            ],
            'baz' => (object) [],
        ]));
    }

    /** @test */
    public function it_can_not_serialize_resources()
    {
        $driver = new JsonDriver();

        $this->expectException(CantBeSerialized::class);

        $resource = tmpfile();

        $driver->serialize($resource);
    }
}
