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

    /**
     * Get all file names in this directory that have the same name
     * as $fileName, but have a different file extension.
     *
     * @param string $fileName
     *
     * @return array
     */
    public function getNamesWithDifferentExtension(string $fileName)
    {
        if (! file_exists($this->basePath)) {
            return [];
        }

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        $baseName = substr($fileName, 0, strlen($fileName) - strlen($extension) - 1);

        $allNames = scandir($this->basePath);

        $namesWithDifferentExtension = array_filter($allNames, function ($existingName) use ($baseName, $extension) {
            $existingExtension = pathinfo($existingName, PATHINFO_EXTENSION);

            $existingBaseName = substr($existingName, 0, strlen($existingName) - strlen($existingExtension) - 1);

            return $existingBaseName === $baseName && $existingExtension !== $extension;
        });

        return array_values($namesWithDifferentExtension);
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

    public function delete(string $fileName)
    {
        return unlink($this->path($fileName));
    }

    public function copy(string $filePath, string $fileName)
    {
        if (! file_exists($this->basePath)) {
            mkdir($this->basePath);
        }

        copy($filePath, $this->path($fileName));
    }

    public function fileEquals(string $filePath, string $fileName)
    {
        return sha1_file($filePath) === sha1_file($this->path($fileName));
    }
}
