<?php

namespace Spatie\Snapshots;

use PHPUnit_Framework_ExpectationFailedException;

trait MatchesSnapshots
{
    public function assertMatchesSnapshot($actual, $type = 'var', $methodTrace = null)
    {
        $snapshot = Snapshot::forTestMethod(
            $methodTrace ?? debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1],
            $type
        );

        if (! $snapshot->exists()) {
            $snapshot->create($actual);

            return $this->markTestIncomplete("Snapshot created for {$snapshot->id()}");
        }

        if (getenv('update_snapshots') == 1) {
            return $this->updateSnapshot($type, $snapshot, $actual);
        }

        $this->doSnapShotAssertion($type, $snapshot, $actual);
    }

    protected function updateSnapshot($type, Snapshot $snapshot, $actual)
    {
        try {
            $this->doSnapShotAssertion($type, $snapshot, $actual);
        } catch (PHPUnit_Framework_ExpectationFailedException $exception) {
            $snapshot->update($actual);

            $this->markTestIncomplete("Snapshot updated for {$snapshot->id()}");
        }
    }

    protected function doSnapShotAssertion($type, Snapshot $snapshot, $actual)
    {
        if ($type === 'xml') {
            return $this->assertXmlStringEqualsXmlString($snapshot->get(), $actual);
        }

        if ($type === 'json') {
            return $this->assertJsonStringEqualsJsonString($snapshot->get(), $actual);
        }

        $this->assertEquals($snapshot->get(), $actual);
    }

    public function assertMatchesXmlSnapshot($actual)
    {
        $this->assertMatchesSnapshot($actual, 'xml', debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]);
    }

    public function assertMatchesJsonSnapshot($actual)
    {
        $this->assertMatchesSnapshot($actual, 'json', debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]);
    }
}
