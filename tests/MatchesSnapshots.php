<?php

namespace Spatie\Snapshots\Test;

trait MatchesSnapshots
{
    private $snapshotCount = 0;

    public function assertMatchesSnapshot($serializable)
    {
        $this->snapshotCount++;

        $snapshotHandler = SnapshotHandler::forTestMethod(
            debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1],
            $this->snapshotCount
        );

        if (! $snapshotHandler->exists()) {
            $snapshotHandler->create($this->serializeForSnapshot($serializable));

            return $this->markTestIncomplete("Snapshot created for {$snapshotHandler->id()}");
        }

        if ($this->shouldUpdateSnapshots()) {
            $snapshotHandler->update($this->serializeForSnapshot($serializable));

            return $this->markTestIncomplete("Snapshot updated for {$snapshotHandler->id()}");
        }

        return $this->assertEquals($snapshotHandler->get(), $serializable);
    }

    /** @after **/
    public function resetSnapshotCounter()
    {
        $this->snapshotCount = 0;
    }

    protected function shouldUpdateSnapshots(): bool
    {
        return getenv('UPDATE_SNAPSHOTS');
    }

    protected function serializeForSnapshot($serializable)
    {
        return $serializable;
    }
}
