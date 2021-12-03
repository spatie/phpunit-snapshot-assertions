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

        $expected = implode("\n", [
            '{',
            '    "foo": "bar",',
            '    "строка в юникоде": "тест"',
            '}',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('{"foo":"bar", "строка в юникоде":"тест"}'));
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
