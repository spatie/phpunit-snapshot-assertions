<?php

namespace Spatie\Snapshots\Test;

trait MatchesSnapshots
{
    private $snapshotCount = 0;

    public function assertMatchesSnapshot($serializable)
    {
        $this->snapshotCount++;

        $snapshot = Snapshot::forTestMethod(
            debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1],
            $this->snapshotCount
        );

        if (! $snapshot->exists()) {
            $snapshot->create($this->serializeForSnapshot($serializable));

            return $this->markTestIncomplete("Snapshot created for {$snapshot->id()}");
        }

        if ($this->updateSnapshots()) {
            $snapshot->update($this->serializeForSnapshot($serializable));

            return $this->markTestIncomplete("Snapshot updated for {$snapshot->id()}");
        }

        return $this->assertEquals($snapshot->get(), $serializable);
    }

    /** @after **/
    public function resetSnapshotCounter()
    {
        $this->snapshotCount = 0;
    }

    protected function hasSnapshot($path, $id): bool
    {
        if (! file_exists($path)) {
            return false;
        }

        $snapshots = require $path;

        return isset($snapshots[$id]);
    }

    protected function getSnapshot($path, $id)
    {
        $snapshots = require $path;

        return $snapshots[$id];
    }

    protected function updateOrCreateSnapshot($path, $id, $serializable)
    {

    }

    protected function serializeForSnapshot($serializable)
    {
        return $serializable;
    }
}
