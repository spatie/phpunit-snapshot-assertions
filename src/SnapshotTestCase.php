<?php

namespace Spatie\Snapshots;

use PHPUnit\Framework\TestCase;

/**
 * Class TestCaseSnapshot.
 *
 * Special test case, supported Windows OS.
 *
 * @see {https://docs.microsoft.com/en-us/windows/desktop/fileio/naming-a-file}
 */
abstract class TestCaseSnapshot extends TestCase
{
    const WINDOWS_FILE_NAME_LENGTH_LIMIT = 255;

    use MatchesSnapshots;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     *
     * @return string
     */
    protected function getSnapshotId(): string
    {
        $name = (new \ReflectionClass($this))->getShortName() . '_' .
            $this->getName() . '_' .
            $this->snapshotIncrementor;

        $replaced = \preg_replace('/(\s+|\W+)/ui', '_', $name);

        if (\mb_strlen($replaced) >= self::WINDOWS_FILE_NAME_LENGTH_LIMIT) {
            throw new \Exception('Very long filename, please rename test or test case name!');
        }

        return $replaced;
    }
}
