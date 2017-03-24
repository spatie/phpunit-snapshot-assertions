<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\VarDriver;

class VarDriverTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_string()
    {
        $driver = new VarDriver();

        $this->assertEquals("<?php return 'foo';".PHP_EOL, $driver->serialize('foo'));
    }

    /** @test */
    public function it_can_serialize_an_integer()
    {
        $driver = new VarDriver();

        $this->assertEquals('<?php return 1;'.PHP_EOL, $driver->serialize(1));
    }

    /** @test */
    public function it_can_serialize_a_float()
    {
        $driver = new VarDriver();

        $this->assertEquals('<?php return 1.5;'.PHP_EOL, $driver->serialize(1.5));
    }

    /** @test */
    public function it_can_serialize_an_array()
    {
        $driver = new VarDriver();

        $expected = implode(PHP_EOL, [
            '<?php return array (',
            "  'foo' => ",
            '  array (',
            "    'bar' => 'baz',",
            '  ),',
            ');',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(['foo' => ['bar' => 'baz']]));
    }

    /** @test */
    public function it_can_serialize_an_object()
    {
        $driver = new VarDriver();

        $expected = implode(PHP_EOL, [
            '<?php return stdClass::__set_state(array(',
            "   'foo' => 'bar',",
            '));',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize((object) ['foo' => 'bar']));
    }

    /** @test */
    public function it_can_serialize_a_class()
    {
        $driver = new VarDriver();

        $expected = implode(PHP_EOL, [
            '<?php return Spatie\\Snapshots\\Test\\Unit\\Drivers\\Dummy::__set_state(array(',
            "   'foo' => 'bar',",
            '));',
            '',
        ]);

        $this->assertEquals($expected, $driver->serialize(new Dummy()));
    }
}

class Dummy
{
    private $foo = 'bar';
}
