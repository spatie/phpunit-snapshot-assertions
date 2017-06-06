<?php

namespace Spatie\Snapshots\Test\Integration;

trait ComparesSnapshotFiles
{
    protected $snapshotsDir = __DIR__.'/__snapshots__';

    protected $snapshotStubsDir = __DIR__.'/stubs/__snapshots__';

    protected $exampleSnapshotsDir = __DIR__.'/stubs/example_snapshots';

    protected function setUpComparesSnapshotFiles()
    {
        $this->emptyDirectory($this->snapshotsDir);

        $this->copyDirectory($this->snapshotStubsDir, $this->snapshotsDir);
    }

    protected function assertSnapshotMatchesExample($snapshotPath, $examplePath)
    {
        $snapshot = $this->snapshotsDir.'/'.$snapshotPath;
        $example = $this->exampleSnapshotsDir.'/'.$examplePath;

        $this->assertFileExists($snapshot);
        $this->assertFileEquals($example, $snapshot);
    }

    protected function emptyDirectory($path)
    {
        if (! file_exists($path)) {
            return true;
        }
        if (! is_dir($path)) {
            return unlink($path);
        }
        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..' || $item == '.gitignore') {
                continue;
            }
            if (! $this->emptyDirectory($path.'/'.$item)) {
                return false;
            }
        }
    }

    protected function copyDirectory($sourcePath, $destinationPath)
    {
        if (! file_exists($destinationPath)) {
            mkdir($destinationPath);
        }

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
