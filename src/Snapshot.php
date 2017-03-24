<?php

namespace Spatie\Snapshots;

use Spatie\Snapshots\Filesystems\LocalFilesystem;

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
        string $namespace,
        string $test,
        string $directory,
        Driver $driver
    ): self {
        $filesystem = LocalFilesystem::inDirectory($directory);

        $id = "{$namespace}__{$test}";

        return new self($id, $filesystem, $driver);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function filename(): string
    {
        return $this->id.$this->driver->extension();
    }

    public function path(): string
    {
        return $this->filesystem->path($this->filename());
    }

    public function exists(): bool
    {
        return $this->filesystem->has($this->filename());
    }

    public function assertMatches($actual)
    {
        $this->driver->match($this->driver->load($this->path()), $actual);
    }

    public function create($actual)
    {
        $this->filesystem->put($this->filename(), $this->driver->serialize($actual));
    }
}
