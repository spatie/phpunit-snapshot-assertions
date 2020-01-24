<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\ObjectDriver;

class ObjectDriverTest extends TestCase
{
    /** @test */
    public function it_can_serialize_an_object()
    {
        $driver = new ObjectDriver();

        $expected = <<<'JSON'
{
    "name": "My name",
    "valid": true,
    "dateTime": "2020-01-01T15:00:00+01:00",
    "public": "public"
}
JSON;

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
