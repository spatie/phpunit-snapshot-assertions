<?php

namespace Spatie\Snapshots;

use PHPUnit\Framework\ExpectationFailedException;
use ReflectionObject;
use Spatie\Snapshots\Concerns\SnapshotDirectoryAware;
use Spatie\Snapshots\Concerns\SnapshotIdAware;
use Spatie\Snapshots\Drivers\HtmlDriver;
use Spatie\Snapshots\Drivers\ImageDriver;
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\Drivers\ObjectDriver;
use Spatie\Snapshots\Drivers\TextDriver;
use Spatie\Snapshots\Drivers\XmlDriver;
use Spatie\Snapshots\Drivers\YamlDriver;

trait MatchesSnapshots
{
    use SnapshotDirectoryAware;
    use SnapshotIdAware;

    protected int $snapshotIncrementor = 0;

    protected array $snapshotChanges = [];

    /** @before */
    public function setUpSnapshotIncrementor()
    {
        $this->snapshotIncrementor = 0;
    }

    /** @postCondition */
    public function markTestIncompleteIfSnapshotsHaveChanged()
    {
        if (empty($this->snapshotChanges)) {
            return;
        }

        if (count($this->snapshotChanges) === 1) {
            $this->markTestIncomplete($this->snapshotChanges[0]);

            return;
        }

        $formattedMessages = implode(PHP_EOL, array_map(function (string $message) {
            return "- {$message}";
        }, $this->snapshotChanges));

        $this->markTestIncomplete($formattedMessages);
    }

    public function assertMatchesSnapshot($actual, ?Driver $driver = null): void
    {
        if (! is_null($driver)) {
            $this->doSnapshotAssertion($actual, $driver);

            return;
        }

        if (is_string($actual) || is_int($actual) || is_float($actual)) {
            $this->doSnapshotAssertion($actual, new TextDriver());

            return;
        }

        $this->doSnapshotAssertion($actual, new ObjectDriver());
    }

    public function assertMatchesFileHashSnapshot(string $filePath): void
    {
        if (! file_exists($filePath)) {
            $this->fail('File does not exist');
        }

        $actual = sha1_file($filePath);

        $this->assertMatchesSnapshot($actual);
    }

    public function assertMatchesFileSnapshot(string $file): void
    {
        $this->doFileSnapshotAssertion($file);
    }

    public function assertMatchesHtmlSnapshot(string $actual): void
    {
        $this->assertMatchesSnapshot($actual, new HtmlDriver());
    }

    public function assertMatchesJsonSnapshot(array|string|null|int|float|bool $actual): void
    {
        $this->assertMatchesSnapshot($actual, new JsonDriver());
    }

    public function assertMatchesObjectSnapshot($actual): void
    {
        $this->assertMatchesSnapshot($actual, new ObjectDriver());
    }

    public function assertMatchesTextSnapshot($actual): void
    {
        $this->assertMatchesSnapshot($actual, new TextDriver());
    }

    public function assertMatchesXmlSnapshot($actual): void
    {
        $this->assertMatchesSnapshot($actual, new XmlDriver());
    }

    public function assertMatchesYamlSnapshot($actual): void
    {
        $this->assertMatchesSnapshot($actual, new YamlDriver());
    }

    public function assertMatchesImageSnapshot(
        $actual,
        float $threshold = 0.1,
        bool $includeAa = true
    ): void {
        $this->assertMatchesSnapshot($actual, new ImageDriver(
            $threshold,
            $includeAa,
        ));
    }

    /*
     * Determines the directory where file snapshots are stored. By default a
     * `__snapshots__/files` directory is created at the same level as the
     * test class.
     */
    protected function getFileSnapshotDirectory(): string
    {
        return $this->getSnapshotDirectory().
            DIRECTORY_SEPARATOR.
            'files';
    }

    /*
     * Determines whether or not the snapshot should be updated instead of
     * matched.
     *
     * Override this method it you want to use a different flag or mechanism
     * than `-d --update-snapshots` or `UPDATE_SNAPSHOTS=true` env var.
     */
    protected function shouldUpdateSnapshots(): bool
    {
        if (in_array('--update-snapshots', $_SERVER['argv'], true)) {
            return true;
        }

        return getenv('UPDATE_SNAPSHOTS') === 'true';
    }

    /*
     * Determines whether or not the snapshot should be created instead of
     * matched.
     *
     * Override this method if you want to use a different flag or mechanism
     * than `-d --without-creating-snapshots` or `CREATE_SNAPSHOTS=false` env var.
     */
    protected function shouldCreateSnapshots(): bool
    {
        return ! in_array('--without-creating-snapshots', $_SERVER['argv'], true)
            && getenv('CREATE_SNAPSHOTS') !== 'false';
    }

    protected function doSnapshotAssertion(mixed $actual, Driver $driver)
    {
        $this->snapshotIncrementor++;

        $snapshot = Snapshot::forTestCase(
            $this->getSnapshotId(),
            $this->getSnapshotDirectory(),
            $driver
        );

        if (! $snapshot->exists()) {
            $this->assertSnapshotShouldBeCreated($snapshot->filename());

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
            }

            return;
        }

        try {
            $snapshot->assertMatches($actual);
        } catch (ExpectationFailedException $exception) {
            $this->rethrowExpectationFailedExceptionWithUpdateSnapshotsPrompt($exception);
        }
    }

    protected function doFileSnapshotAssertion(string $filePath): void
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
        $snapshotId = Filename::cleanFilename($snapshotId);

        // If $filePath has a different file extension than the snapshot, the test should fail
        if ($namesWithDifferentExtension = $fileSystem->getNamesWithDifferentExtension($snapshotId)) {
            // There is always only one existing snapshot with a different extension
            $existingSnapshotId = $namesWithDifferentExtension[0];

            if ($this->shouldUpdateSnapshots()) {
                $fileSystem->delete($existingSnapshotId);

                $fileSystem->copy($filePath, $snapshotId);

                $this->registerSnapshotChange("File snapshot updated for {$snapshotId}");

                return;
            }

            $expectedExtension = pathinfo($existingSnapshotId, PATHINFO_EXTENSION);

            $this->fail("File did not match the snapshot file extension (expected: {$expectedExtension}, was: {$fileExtension})");
        }

        $failedSnapshotId = $snapshotId.'_failed.'.$fileExtension;

        if ($fileSystem->has($failedSnapshotId)) {
            $fileSystem->delete($failedSnapshotId);
        }

        if (! $fileSystem->has($snapshotId)) {
            $this->assertSnapshotShouldBeCreated($failedSnapshotId);

            $fileSystem->copy($filePath, $snapshotId);

            $this->registerSnapshotChange("File snapshot created for {$snapshotId}");

            return;
        }

        if (! $fileSystem->fileEquals($filePath, $snapshotId)) {
            if ($this->shouldUpdateSnapshots()) {
                $fileSystem->copy($filePath, $snapshotId);

                $this->registerSnapshotChange("File snapshot updated for {$snapshotId}");

                return;
            }

            $fileSystem->copy($filePath, $failedSnapshotId);

            $this->fail("File did not match snapshot ({$snapshotId})");
        }

        $this->assertTrue(true);
    }

    protected function createSnapshotAndMarkTestIncomplete(Snapshot $snapshot, $actual): void
    {
        $snapshot->create($actual);

        $this->registerSnapshotChange("Snapshot created for {$snapshot->id()}");
    }

    protected function updateSnapshotAndMarkTestIncomplete(Snapshot $snapshot, $actual): void
    {
        $snapshot->create($actual);

        $this->registerSnapshotChange("Snapshot updated for {$snapshot->id()}");
    }

    protected function rethrowExpectationFailedExceptionWithUpdateSnapshotsPrompt($exception): void
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

    protected function registerSnapshotChange(string $message): void
    {
        $this->snapshotChanges[] = $message;
    }

    protected function assertSnapshotShouldBeCreated(string $snapshotFileName): void
    {
        if ($this->shouldCreateSnapshots()) {
            return;
        }

        $this->fail(
            "Snapshot \"$snapshotFileName\" does not exist.\n".
            'You can automatically create it by removing '.
            'the `CREATE_SNAPSHOTS=false` env var, or '.
            '`-d --without-creating-snapshots` of PHPUnit\'s CLI arguments.'
        );
    }
}
