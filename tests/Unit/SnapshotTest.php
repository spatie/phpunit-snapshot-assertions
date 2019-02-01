<?php

namespace Spatie\Snapshots\Test\Unit;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Filesystem;
use Spatie\Snapshots\Snapshot;

class SnapshotTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $filesystem;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $driver;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->createMock(Filesystem::class);
        $this->driver = $this->createMock(Driver::class);
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
        $this->driver
            ->expects($this->once())
            ->method('extension')
            ->willReturn('php');

        $snapshot = new Snapshot('abc', $this->filesystem, $this->driver);

        $this->assertEquals('abc.php', $snapshot->filename());
    }

    /** @test */
    public function it_has_a_filename_which_is_valid_on_all_systems()
    {
        $this->driver
            ->expects($this->once())
            ->method('extension')
            ->willReturn('php');

        $snapshot = new Snapshot('ClassTest__testOne with data set "Empty"', $this->filesystem, $this->driver);

        $this->assertEquals('ClassTest__testOne with data set Empty.php', $snapshot->filename());
    }
}
