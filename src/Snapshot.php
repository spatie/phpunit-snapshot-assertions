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
        string $id,
        Filesystem $filesystem,
        Driver $driver
    ) {
        $this->id = $id;
        $this->filesystem = $filesystem;
        $this->driver = $driver;
    }

    public static function forTestCase(
        string $id,
        string $directory,
        Driver $driver
    ): self {
        $filesystem = Filesystem::inDirectory($directory);

        return new self($id, $filesystem, $driver);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function filename(): string
    {
        $file = $this->id.'.'.$this->driver->extension();

        return Filename::cleanFilename($file);
    }

    public function exists(): bool
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
