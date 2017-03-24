<?php

namespace Spatie\Snapshots;

interface Filesystem
{
    public function path(string $filename): string ;
    public function has(string $filename): bool;
    public function read(string $filename): bool;
    public function put(string $filename, string $contents);
}
