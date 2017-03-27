<?php

namespace Spatie\Snapshots;

use PHPUnit\Framework\ExpectationFailedException;
use ReflectionClass;
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\Drivers\VarDriver;
use Spatie\Snapshots\Drivers\XmlDriver;

trait MatchesSnapshots
{
    public function assertMatchesSnapshot($actual, Driver $driver = null)
    {
        $snapshot = $this->createSnapshotWithDriver($driver ?? new VarDriver());

        $this->doSnapShotAssertion($snapshot, $actual);
    }

    public function assertMatchesXmlSnapshot($actual)
    {
        $this->assertMatchesSnapshot($actual, new XmlDriver());
    }

    public function assertMatchesJsonSnapshot($actual)
    {
        $this->assertMatchesSnapshot($actual, new JsonDriver());
    }

    /**
     * Determines the snapshot's test, which is the first part of the ID.
     * By default, the test class' name is used.
     *
     * @return string
     */
    protected function getSnapshotTestName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * Determines the snapshot's test case, which is the second part of the ID.
     * By default, the test case's method name is used.
     *
     * @return string
     */
    protected function getSnapshotTestCaseName(): string
    {
        return $this->getName();
    }

    /**
     * Determines the directory where snapshots are stored. By default a
     * `__snapshots__` directory is created at the same level as the test
     * class.
     *
     * @return string
     */
    protected function getSnapshotDirectory(): string
    {
        return dirname((new ReflectionClass($this))->getFileName()).
            DIRECTORY_SEPARATOR.
            '__snapshots__';
    }

    /**
     * Determines whether or not the snapshot should be updated instead of
     * matched.
     *
     * Override this method it you want to use a different flag or mechanism
     * than `-d --update-snapshots`.
     *
     * @return bool
     */
    protected function shouldUpdateSnapshot(): bool
    {
        return in_array('--update-snapshots', $_SERVER['argv'], true);
    }

    protected function createSnapshotWithDriver(Driver $driver): Snapshot
    {
        return Snapshot::forTestCase(
            $this->getSnapshotTestName(),
            $this->getSnapshotTestCaseName(),
            $this->getSnapshotDirectory(),
            $driver
        );
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
}
