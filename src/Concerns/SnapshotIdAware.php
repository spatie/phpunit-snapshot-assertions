<?php

namespace Spatie\Snapshots\Concerns;

use PHPUnit\Framework\Attributes\Before;
use ReflectionClass;

trait SnapshotIdAware
{
    protected int $snapshotIncrementor = 0;

    #[Before]
    public function setUpSnapshotIncrementor()
    {
        $this->snapshotIncrementor = 0;
    }

    /*
     * Determines the snapshot's id. By default, the test case's class and
     * method names are used.
     *
     * If an explicit `$id` is provided, it will be prefixed with 's-' to
     * distinguish it from auto-generated incrementor-based IDs. This avoids
     * conflicts, should an explicit `$id` be numeric.
     */
    protected function getSnapshotId(?string $id = null): string
    {
        if ($id !== null) {
            $suffix = 's-'.$id;
        } else {
            $this->snapshotIncrementor++;
            $suffix = $this->snapshotIncrementor;
        }

        return (new ReflectionClass($this))->getShortName().'__'.
            $this->nameWithDataSet().'__'.
            $suffix;
    }
}
