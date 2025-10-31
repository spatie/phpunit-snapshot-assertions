<?php

namespace Spatie\Snapshots\Concerns;

use ReflectionClass;

trait SnapshotIdAware
{
    private ?string $snapshotId = null;

    /*
     * Determines the snapshot's id. By default, the test case's class and
     * method names are used.
     */
    protected function getSnapshotId(): string
    {
        if ($this->snapshotId !== null) {
            return $this->snapshotId;
        }

        return (new ReflectionClass($this))->getShortName().'__'.
            $this->nameWithDataSet().'__'.
            $this->snapshotIncrementor;
    }

    protected function setSnapshotId(string $snapshotId): void
    {
        $this->snapshotId = $snapshotId;
    }
}
