<?php

namespace Spatie\Snapshots\Test\Integration;

use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase;
use Spatie\Snapshots\Laravel\SnapshotsServiceProvider;
use Spatie\Snapshots\MatchesSnapshots;

class TestResponseTest extends TestCase
{
    use MatchesSnapshots;

    protected function getPackageProviders($app)
    {
        return [
            SnapshotsServiceProvider::class,
        ];
    }

    /** @test */
    public function it_can_assert_matches_html_snapshot()
    {
        TestResponse::fromBaseResponse(response()->noContent())->assertMatchesHtmlSnapshot();
    }
}
