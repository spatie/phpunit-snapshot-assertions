<?php

namespace Spatie\Snapshots;

use ReflectionClass;

class Snapshot
{
    /** @var string */
    private $directory;

    /** @var string */
    private $id;

    /** @var \Spatie\Snapshots\Driver */
    private $driver;

    private function __construct(string $directory, string $id, Driver $driver)
    {
        $this->directory = $directory;
        $this->id = $id;
        $this->driver = $driver;
    }

    public static function forTestMethod($backtrace, Driver $driver): self
    {
        $class = new ReflectionClass($backtrace['class']);
        $method = $backtrace['function'];

        $directory = dirname($class->getFileName()).'/__snapshots__';
        $id = "{$class->getShortName()}__{$method}";

        return new self($directory, $id, $driver);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function path(): string
    {
        return $this->directory.DIRECTORY_SEPARATOR.$this->id.'.'.$this->driver->extension();
    }

    public function exists(): bool
    {
        return file_exists($this->path());
    }

    public function assertMatches($actual)
    {
        $this->driver->match($this->driver->load($this->path()), $actual);
    }

    public function create($actual)
    {
        if (! file_exists($this->directory)) {
            mkdir($this->directory);
        }

        file_put_contents($this->path(), $this->driver->serialize($actual));
    }
}
