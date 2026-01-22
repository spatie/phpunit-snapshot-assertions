<?php

namespace Spatie\Snapshots\Concerns;

use ReflectionClass;

trait SnapshotIdAware
{
    use PhpUnitCompatibility;

    /*
     * Determines the snapshot's id. By default, the test case's class and
     * method names are used.
     *
     * If an explicit $id is provided, it will be prefixed with 's-' to
     * distinguish it from auto-generated incrementor-based IDs.
     */
    protected function getSnapshotId(?string $id = null): string
    {
        $suffix = $id !== null ? 's-'.$id : $this->snapshotIncrementor;

        return (new ReflectionClass($this))->getShortName().'__'.
            $this->nameWithDataSet().'__'.
            $suffix;
    }
}
