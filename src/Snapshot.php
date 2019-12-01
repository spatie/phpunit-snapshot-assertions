<?php

namespace Spatie\Snapshots;

use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Filesystem;

class Snapshot
{
    private string $id;

    private Filesystem $filesystem;

    private Driver $driver;

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
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;[]().
        $file = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file);
        // Remove any runs of periods
        $file = preg_replace("([\.]{2,})", '', $file);

        return $file;
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
