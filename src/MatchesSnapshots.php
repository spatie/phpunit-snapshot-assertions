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

    public function assertMatchesSnapshot($actual, Driver $driver = null)
    {
        $this->doSnapshotAssertion($actual, $driver ?? new VarDriver());
    }

    public function assertMatchesXmlSnapshot($actual)
    {
        $this->assertMatchesSnapshot($actual, new XmlDriver());
    }

    public function assertMatchesJsonSnapshot($actual)
    {
        $this->assertMatchesSnapshot($actual, new JsonDriver());
    }

    public function assertMatchesFileHashSnapshot($filePath)
    {
        if (! file_exists($filePath)) {
            $this->fail('File does not exist');
        }

        $actual = sha1_file($filePath);

        $this->assertMatchesSnapshot($actual);
    }

    public function assertMatchesFileSnapshot($file)
    {
        $this->doFileSnapshotAssertion($file);
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
     * Determines the directory where file snapshots are stored. By default a
     * `__snapshots__/files` directory is created at the same level as the
     * test class.
     *
     * @return string
     */
    protected function getFileSnapshotDirectory(): string
    {
        return $this->getSnapshotDirectory().
            DIRECTORY_SEPARATOR.
            'files';
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

    protected function doSnapshotAssertion($actual, Driver $driver)
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
                $snapshot->assertMatches($actual);
            } catch (ExpectationFailedException $exception) {
                $this->updateSnapshotAndMarkTestIncomplete($snapshot, $actual);
            } catch (PHPUnit_Framework_ExpectationFailedException $exception) {
                $this->updateSnapshotAndMarkTestIncomplete($snapshot, $actual);
            }
        }

        try {
            $snapshot->assertMatches($actual);
        } catch (ExpectationFailedException $exception) {
            $this->rethrowExpectationFailedExceptionWithUpdateSnapshotsPrompt($exception);
        } catch (PHPUnit_Framework_ExpectationFailedException $exception) {
            $this->rethrowExpectationFailedExceptionWithUpdateSnapshotsPrompt($exception);
        }
    }

    protected function doFileSnapshotAssertion(string $filePath)
    {
        if (! file_exists($filePath)) {
            $this->fail('File does not exist');
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        if (empty($fileExtension)) {
            $this->fail("Unable to make a file snapshot, file does not have a file extension ({$filePath})");
        }

        $fileSystem = Filesystem::inDirectory($this->getFileSnapshotDirectory());

        $this->snapshotIncrementor++;

        $snapshotId = $this->getSnapshotId().'.'.$fileExtension;

        // If $filePath has a different file extension than the snapshot, the test should fail
        if ($namesWithDifferentExtension = $fileSystem->getNamesWithDifferentExtension($snapshotId)) {
            // There is always only one existing snapshot with a different extension
            $existingSnapshotId = $namesWithDifferentExtension[0];

            if ($this->shouldUpdateSnapshots()) {
                $fileSystem->delete($existingSnapshotId);

                $fileSystem->copy($filePath, $snapshotId);

                return $this->markTestIncomplete("File snapshot updated for {$snapshotId}");
            }

            $expectedExtension = pathinfo($existingSnapshotId, PATHINFO_EXTENSION);

            return $this->fail("File did not match the snapshot file extension (expected: {$expectedExtension}, was: {$fileExtension})");
        }

        $failedSnapshotId = $snapshotId.'_failed.'.$fileExtension;

        if ($fileSystem->has($failedSnapshotId)) {
            $fileSystem->delete($failedSnapshotId);
        }

        if (! $fileSystem->has($snapshotId)) {
            $fileSystem->copy($filePath, $snapshotId);

            $this->markTestIncomplete("File snapshot created for {$snapshotId}");
        }

        if (! $fileSystem->fileEquals($filePath, $snapshotId)) {
            if ($this->shouldUpdateSnapshots()) {
                $fileSystem->copy($filePath, $snapshotId);

                $this->markTestIncomplete("File snapshot updated for {$snapshotId}");
            }

            $fileSystem->copy($filePath, $failedSnapshotId);

            $this->fail("File did not match snapshot ({$snapshotId})");
        }

        $this->assertTrue(true);
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
