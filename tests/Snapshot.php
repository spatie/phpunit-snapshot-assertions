<?php

namespace Spatie\Snapshots\Test;

use ReflectionClass;

class Snapshot
{
    /** @var string */
    protected $directory, $filename, $id;

    /** @var array */
    protected $availableSnapshots;

    private function __construct(string $directory, string $filename, string $id)
    {
        $this->directory = $directory;
        $this->filename = $filename;
        $this->id = $id;

        $this->availableSnapshots = (@include $this->path()) ?: [];
    }

    public static function forTestMethod($backtrace, $count): self
    {
        $class = new ReflectionClass($backtrace['class']);
        $method = $backtrace['function'];

        $directory = dirname($class->getFileName()).'/__snapshots__';
        $filename = $class->getShortName();
        $id = "{$method} {$count}";

        return new self($directory, $filename, $id);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function path(): string
    {
        return "{$this->directory}/{$this->filename}";
    }

    public function exists(): bool
    {
        return isset($this->availableSnapshots[$this->id]);
    }

    public function get()
    {
        $this->availableSnapshots[$this->id];
    }

    public function create($serializedValue)
    {
        $this->availableSnapshots[$this->id] = $serializedValue;

        if (! file_exists($this->directory)) {
            mkdir($this->directory);
        }

        $contents = '<?php\n\n'.print_r($this->availableSnapshots, true);

        file_put_contents($this->path(), $contents);
    }

    public function update($serializedValue)
    {
        $this->create($serializedValue);
    }
}
