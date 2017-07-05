<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_ExpectationFailedException;
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

    /** @test */
    public function it_can_set_custom_error_message()
    {
        $driver = new VarDriver();

        $customMessage = 'custom string error message';

        try {
            $driver->match('Foo', 'Bar', $customMessage);
        } catch (ExpectationFailedException $e) {
            $this->assertNotSame(false, strpos($e->getMessage(), $customMessage), 'Failed to find custom string error message');
            return;
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertNotSame(false, strpos($e->getMessage(), $customMessage), 'Failed to find custom string error message');
            return;
        }

        /* Mark test as incomplete if we don't get a ExpectationFailedException */
        $this->markTestIncomplete('Expected exception did not occur');
    }
}

class Dummy
{
    private $foo = 'bar';
}
