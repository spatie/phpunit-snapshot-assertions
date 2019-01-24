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
    public function it_can_serialize_a_json_hash_to_pretty_json()
    {
        $driver = new JsonDriver();

        $expected = implode(PHP_EOL, [
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

        $expected = implode(PHP_EOL, [
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
                'ccc' => ['xxx', 'yyy', 'zzz']
            ]
        ]));
    }

    /** @test */
    public function it_can_serialize_a_json_array_to_pretty_json()
    {
        $driver = new JsonDriver();

        $expected = implode(PHP_EOL, [
            '[',
            '    "foo",',
            '    "bar",',
            '    "baz"',
            ']',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(['foo', 'bar', 'baz']));
    }
}
