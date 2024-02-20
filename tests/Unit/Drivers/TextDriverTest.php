<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\TextDriver;

class TextDriverTest extends TestCase
{
    #[Test]
    public function it_can_serialize_laravel_route_list()
    {
        $driver = new TextDriver();

        $expected = implode("\n", [
            '',
            '  GET|HEAD       / ..................................................... index',
            '',
            '                                                            Showing [1] routes',
        ]);

        $this->assertEquals($expected, $driver->serialize(<<<'EOF'

  GET|HEAD       / ..................................................... index

                                                            Showing [1] routes
EOF));
    }

    #[Test]
    public function it_can_serialize_when_given_OS_dependant_line_endings()
    {
        $driver = new TextDriver();

        $expected = implode("\n", [
            '',
            '  GET|HEAD       / ..................................................... index',
            '',
            '                                                            Showing [1] routes',
        ]);

        // Due to using PHP_EOL this should fail (conditionally) when run on windows
        $actual = implode(PHP_EOL, [
            '',
            '  GET|HEAD       / ..................................................... index',
            '',
            '                                                            Showing [1] routes',
        ]);

        $this->assertEquals($expected, $driver->serialize($actual));
    }
}
