<?php

namespace Spatie\Snapshots\Concerns;

use ReflectionClass;

trait SnapshotIdAware
{
    use PhpUnitCompatibility;

    /*
     * Determines the snapshot's id. By default, the test case's class and
     * method names are used.
     */
    protected function getSnapshotId(): string
    {
        return (new ReflectionClass($this))->getShortName().'__'.
            $this->nameWithDataSet().'__'.
            $this->snapshotIncrementor;
    }
}
