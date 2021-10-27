<?php

namespace Spatie\Snapshots\Test\Integration;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CustomJsonEncodeFlagsTest extends TestCase
{
    use MatchesSnapshots;

    protected function getJsonEncodeFlags(): int
    {
        return JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
    }

    /** @test */
    public function can_match_an_array_snapshot()
    {
        $data = ['foo' => 'foo', 'bar' => 'bar', 'baz' => '🍭'];

        $this->assertMatchesJsonSnapshot($data);
    }
}
