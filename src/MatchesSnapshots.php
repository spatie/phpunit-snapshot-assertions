<?php

namespace Spatie\Snapshots;

use PHPUnit\Framework\ExpectationFailedException;
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\Drivers\VarDriver;
use Spatie\Snapshots\Drivers\XmlDriver;

trait MatchesSnapshots
{
    public function assertMatchesSnapshot($actual, Driver $driver = null, $methodTrace = null)
    {
        $snapshot = Snapshot::forTestMethod(
            $methodTrace ?? debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1],
            $driver ?? new VarDriver()
        );

        $this->doSnapShotAssertion($snapshot, $actual);
    }

    public function assertMatchesXmlSnapshot($actual)
    {
        $this->assertMatchesSnapshot($actual, new XmlDriver(), debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]);
    }

    public function assertMatchesJsonSnapshot($actual)
    {
        $this->assertMatchesSnapshot($actual, new JsonDriver(), debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]);
    }

    protected function doSnapShotAssertion(Snapshot $snapshot, $actual)
    {
        if (! $snapshot->exists()) {
            $snapshot->create($actual);

            return $this->markTestIncomplete("Snapshot created for {$snapshot->id()}");
        }

        if ($this->shouldUpdateSnapshot()) {
            try {
                // We only want to update snapshots which need updating. If the snapshot doesn't
                // match the expected output, we'll catch the failure, create a new snapshot and
                // mark the test as incomplete.
                $snapshot->assertMatches($actual);
            } catch (ExpectationFailedException $exception) {
                $snapshot->create($actual);

                return $this->markTestIncomplete("Snapshot updated for {$snapshot->id()}");
            }
        }

        $snapshot->assertMatches($actual);
    }

    protected function shouldUpdateSnapshot(): bool
    {
        return in_array('--update-snapshots', $_SERVER['argv'], true);
    }
}
