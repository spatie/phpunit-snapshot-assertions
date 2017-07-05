<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\ExpectationFailedException;
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

    /** @test */
    public function it_can_set_custom_error_message()
    {
        $driver = new JsonDriver();

        $customMessage = 'custom JSON error message';

        try {
            $driver->match('{"foo":"foo"}', '{"bar":"bar"}', $customMessage);
        } catch (ExpectationFailedException $e) {
            $this->assertNotSame(false, strpos($e->getMessage(), $customMessage), 'Failed to find custom JSON error message');
            return;
        }

        /** Mark test as failed if we don't get a ExpectationFailedException */
        throw new ExpectationFailedException('ExpectationFailedException did not occur');
    }
}
