<?php

namespace Spatie\Snapshots;

class Filesystem
{
    /** @var string */
    private $basePath;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    public static function inDirectory($path)
    {
        return new self($path);
    }

    public function path($filename)
    {
        return $this->basePath.DIRECTORY_SEPARATOR.$filename;
    }

    public function has($filename)
    {
        return file_exists($this->path($filename));
    }

    public function read($filename)
    {
        return file_get_contents($this->path($filename));
    }

    public function put($filename, $contents)
    {
        if (! file_exists($this->basePath)) {
            mkdir($this->basePath);
        }

        file_put_contents($this->path($filename), $contents);
    }
}
