<?php

namespace Spatie\Snapshots\Test;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function true_is_true()
    {
        $data = 'Foo';

        $this->assertMatchesSnapshot($data);
    }
}
