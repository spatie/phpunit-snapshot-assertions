<?php

namespace Spatie\Snapshots\Test\Integration;

trait ComparesSnapshotFiles
{
    protected $snapshotsDir = __DIR__.'/__snapshots__';

    protected $snapshotStubsDir = __DIR__.'/stubs/__snapshots__';

    protected $exampleSnapshotsDir = __DIR__.'/stubs/example_snapshots';

    protected function setUpComparesSnapshotFiles()
    {
        $this->deleteDirectory($this->snapshotsDir);

        $this->copyDirectory($this->snapshotStubsDir, $this->snapshotsDir);
    }

    protected function assertSnapshotMatchesExample($snapshotPath, $examplePath)
    {
        $snapshot = $this->snapshotsDir.'/'.$snapshotPath;
        $example = $this->exampleSnapshotsDir.'/'.$examplePath;

        return $this->assertFileEquals($example, $snapshot);
    }

    protected function deleteDirectory(string $path): bool
    {
        if (! file_exists($path)) {
            return true;
        }
        if (! is_dir($path)) {
            return unlink($path);
        }
        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (! $this->deleteDirectory($path.'/'.$item)) {
                return false;
            }
        }

        return rmdir($path);
    }

    protected function copyDirectory(string $sourcePath, string $destinationPath)
    {
        mkdir($destinationPath);

        $sourceDirectory = opendir($sourcePath);

        while (($file = readdir($sourceDirectory)) !== false) {

            if (in_array($file, ['.', '..'])) {
                continue;
            }

            if (is_dir($sourcePath.'/'.$file)) {
                $this->copyDirectory($sourcePath.'/'.$file, $destinationPath.'/'.$file);
                continue;
            }

            copy($sourcePath.'/'.$file, $destinationPath.'/'.$file);
        }

        closedir($sourceDirectory);
    }
}
