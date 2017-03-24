<?php

namespace Spatie\Snapshots\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Filesystem;
use Spatie\Snapshots\Snapshot;

class SnapshotTest extends TestCase
{
    /** @var \Mockery\MockInterface */
    private $filesystem;

    /** @var \Mockery\MockInterface */
    private $driver;

    public function setUp()
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->driver = Mockery::mock(Driver::class);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_has_an_id()
    {
        $snapshot = new Snapshot('abc', $this->filesystem, $this->driver);

        $this->assertEquals('abc', $snapshot->id());
    }

    /** @test */
    public function it_has_a_filename_based_on_its_id_and_its_drivers_extension()
    {
        $this->driver->shouldReceive('extension')->andReturn('.php');
        
        $snapshot = new Snapshot('abc', $this->filesystem, $this->driver);
        
        $this->assertEquals('abc.php', $snapshot->filename());
    }
}
