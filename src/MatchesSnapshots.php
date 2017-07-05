<?php

namespace Spatie\Snapshots;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit_Framework_ExpectationFailedException;
use ReflectionClass;
use ReflectionObject;
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\Drivers\VarDriver;
use Spatie\Snapshots\Drivers\XmlDriver;

trait MatchesSnapshots
{
    /** @var int */
    protected $snapshotIncrementor;

    /** @before */
    public function setUpSnapshotIncrementor()
    {
        $this->snapshotIncrementor = 0;
    }

    public function assertMatchesSnapshot($actual, Driver $driver = null, string $message = '')
    {
        $this->doSnapshotAssertion($actual, $driver ?? new VarDriver(), $message);
    }

    public function assertMatchesXmlSnapshot($actual, string $message = '')
    {
        $this->assertMatchesSnapshot($actual, new XmlDriver());
    }

    public function assertMatchesJsonSnapshot($actual, string $message = '')
    {
        $this->assertMatchesSnapshot($actual, new JsonDriver());
    }

    /**
     * Determines the snapshot's id. By default, the test case's class and
     * method names are used.
     *
     * @return string
     */
    protected function getSnapshotId(): string
    {
        return (new ReflectionClass($this))->getShortName().'__'.
            $this->getName().'__'.
            $this->snapshotIncrementor;
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
    protected function shouldUpdateSnapshots(): bool
    {
        return in_array('--update-snapshots', $_SERVER['argv'], true);
    }

    protected function doSnapshotAssertion($actual, Driver $driver, string $message = '')
    {
        $this->snapshotIncrementor++;

        $snapshot = Snapshot::forTestCase(
            $this->getSnapshotId(),
            $this->getSnapshotDirectory(),
            $driver
        );

        if (! $snapshot->exists()) {
            $this->createSnapshotAndMarkTestIncomplete($snapshot, $actual);
        }

        if ($this->shouldUpdateSnapshots()) {
            try {
                // We only want to update snapshots which need updating. If the snapshot doesn't
                // match the expected output, we'll catch the failure, create a new snapshot and
                // mark the test as incomplete.
                $snapshot->assertMatches($actual, $message);
            } catch (ExpectationFailedException $exception) {
                $this->updateSnapshotAndMarkTestIncomplete($snapshot, $actual);
            } catch (PHPUnit_Framework_ExpectationFailedException $exception) {
                $this->updateSnapshotAndMarkTestIncomplete($snapshot, $actual);
            }
        }

        try {
            $snapshot->assertMatches($actual, $message);
        } catch (ExpectationFailedException $exception) {
            $this->rethrowExpectationFailedExceptionWithUpdateSnapshotsPrompt($exception);
        } catch (PHPUnit_Framework_ExpectationFailedException $exception) {
            $this->rethrowExpectationFailedExceptionWithUpdateSnapshotsPrompt($exception);
        }
    }

    protected function createSnapshotAndMarkTestIncomplete(Snapshot $snapshot, $actual)
    {
        $snapshot->create($actual);

        $this->markTestIncomplete("Snapshot created for {$snapshot->id()}");
    }

    protected function updateSnapshotAndMarkTestIncomplete(Snapshot $snapshot, $actual)
    {
        $snapshot->create($actual);

        $this->markTestIncomplete("Snapshot updated for {$snapshot->id()}");
    }

    protected function rethrowExpectationFailedExceptionWithUpdateSnapshotsPrompt($exception)
    {
        $newMessage = $exception->getMessage()."\n\n".
            'Snapshots can be updated by passing '.
            '`-d --update-snapshots` through PHPUnit\'s CLI arguments.';

        $exceptionReflection = new ReflectionObject($exception);

        $messageReflection = $exceptionReflection->getProperty('message');
        $messageReflection->setAccessible(true);
        $messageReflection->setValue($exception, $newMessage);

        throw $exception;
    }
}
