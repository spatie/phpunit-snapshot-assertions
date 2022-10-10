<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\HtmlDriver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class HtmlDriverTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_html_string_to_pretty_html()
    {
        $driver = new HtmlDriver();

        $expected = implode("\n", [
            '<!DOCTYPE html>',
            '<html lang="en">',
            '<head></head>',
            '<body><h1>Hello, world!</h1></body>',
            '</html>',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('<!doctype html><html lang="en"><head></head><body><h1>Hello, world!</h1></body></html>'));
    }

    /** @test */
    public function test_for_issue_140()
    {
        $driver = new HtmlDriver();

        $expected = implode("\n", [
            '<!DOCTYPE html>',
            '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">',
            '<head>',
            '<!--[if !mso]><!-->',
            '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
            '<!--<![endif]-->',
            '</head>',
            '<body><h1>Hello, world!</h1></body>',
            '</html>',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize($expected));
    }

    /** @test */
    public function it_can_serialize_a_html_string_without_a_doctype()
    {
        $driver = new HtmlDriver();

        $expected = implode("\n", [
            '<html lang="en">',
            '<head></head>',
            '<body><h1>Hello, world!</h1></body>',
            '</html>',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize('<html lang="en"><head></head><body><h1>Hello, world!</h1></body></html>'));
    }

    /** @test */
    public function it_can_only_serialize_strings()
    {
        $driver = new HtmlDriver();

        $this->expectException(CantBeSerialized::class);

        $driver->serialize(['foo' => 'bar']);
    }

    /** @test */
    public function it_can_serialize_an_empty_string()
    {
        $driver = new HtmlDriver();

        $this->assertEquals("\n", $driver->serialize(''));
    }
}
