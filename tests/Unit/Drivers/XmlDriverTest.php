<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_ExpectationFailedException;
use Spatie\Snapshots\Drivers\XmlDriver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class XmlDriverTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_json_string_to_pretty_json()
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

    /** @test */
    public function it_can_set_custom_error_message()
    {
        $driver = new XmlDriver();

        $customMessage = 'custom XML error message';

        try {
            $driver->match('<foo><bar>baz</bar></foo>', '<baz><bar>foo</bar></baz>', $customMessage);
        } catch (ExpectationFailedException $e) {
            $this->assertNotSame(false, strpos($e->getMessage(), $customMessage), 'Failed to find custom XML error message');
            return;
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertNotSame(false, strpos($e->getMessage(), $customMessage), 'Failed to find custom XML error message');
            return;
        }

        /* Mark test as incomplete if we don't get a ExpectationFailedException */
        $this->markTestIncomplete('Expected exception did not occur');
    }
}
