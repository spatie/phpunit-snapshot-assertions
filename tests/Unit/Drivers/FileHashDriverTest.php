<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use Exception;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\FileHashDriver;

class FileHashDriverTest extends TestCase
{
    /** @test */
    public function it_can_hash_a_file()
    {
        $driver = new FileHashDriver();

        $filePath = __DIR__.'/files/example-file.txt';

        $expected = sha1_file($filePath);

        $this->assertEquals($expected, $driver->serialize($filePath));
    }

    /** @test */
    public function it_throws_an_exception_if_the_file_does_not_exist()
    {
        $driver = new FileHashDriver();

        $this->expectException(Exception::class);

        $driver->serialize(__DIR__.'/files/fake-file-path');
    }
}
