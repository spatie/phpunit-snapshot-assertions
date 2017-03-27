<?php

namespace Spatie\Snapshots;

class Filesystem
{
    /** @var string */
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public static function inDirectory(string $path): self
    {
        return new self($path);
    }

    public function path(string $filename): string
    {
        return $this->basePath.DIRECTORY_SEPARATOR.$filename;
    }

    public function has(string $filename): bool
    {
        return file_exists($this->path($filename));
    }

    public function read(string $filename): string
    {
        return file_get_contents($this->path($filename));
    }

    public function put(string $filename, string $contents)
    {
        if (! file_exists($this->basePath)) {
            mkdir($this->basePath);
        }

        file_put_contents($this->path($filename), $contents);
    }
}
