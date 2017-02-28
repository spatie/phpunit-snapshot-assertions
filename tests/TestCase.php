<?php

namespace Spatie\Snapshots\Test;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function deleteDirectory(string $path): bool
    {
        if (!file_exists($path)) {
            return true;
        }
        if (!is_dir($path)) {
            return unlink($path);
        }
        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($path . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($path);
    }

    protected function recursiveCopy(string $src, string $dst)
    {
        $ds = DIRECTORY_SEPARATOR;
        $dir = opendir($src);

        mkdir($dst);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src.$ds.$file)) {
                    $this->recursiveCopy($src.$ds.$file, $dst.$ds.$file);
                } else {
                    copy($src.$ds.$file, $dst.$ds.$file);
                }
            }
        }

        closedir($dir);
    }
}
