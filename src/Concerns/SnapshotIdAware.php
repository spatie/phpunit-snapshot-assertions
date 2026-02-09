<?php

namespace Spatie\Snapshots\Concerns;

use PHPUnit\Framework\Attributes\Before;
use ReflectionClass;

trait SnapshotIdAware
{
    use PhpUnitCompatibility;

    protected int $snapshotIncrementor = 0;

    /** @before */
    #[Before]
    public function setUpSnapshotIncrementor()
    {
        $this->snapshotIncrementor = 0;
    }

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
