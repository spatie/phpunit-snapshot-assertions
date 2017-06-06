<?php

namespace Spatie\Snapshots;

class Snapshot
{
    /** @var string */
    private $id;

    /** @var \Spatie\Snapshots\Filesystem */
    private $filesystem;

    /** @var \Spatie\Snapshots\Driver */
    private $driver;

    public function __construct(
        $id,
        Filesystem $filesystem,
        Driver $driver
    ) {
        $this->id = $id;
        $this->filesystem = $filesystem;
        $this->driver = $driver;
    }

    public static function forTestCase(
        $id,
        $directory,
        Driver $driver
    ) {
        $filesystem = Filesystem::inDirectory($directory);

        return new self($id, $filesystem, $driver);
    }

    public function id()
    {
        return $this->id;
    }

    public function filename()
    {
        return $this->id.'.'.$this->driver->extension();
    }

    public function exists()
    {
        return $this->filesystem->has($this->filename());
    }

    public function assertMatches($actual)
    {
        $this->driver->match($this->filesystem->read($this->filename()), $actual);
    }

    public function create($actual)
    {
        $this->filesystem->put($this->filename(), $this->driver->serialize($actual));
    }
}
