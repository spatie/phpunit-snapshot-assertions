<?php

namespace Spatie\Snapshots\Test\Integration;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class AssertionTest extends TestCase
{
    use ComparesSnapshotFiles;
    use MatchesSnapshots;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpComparesSnapshotFiles();
    }

    /** @test */
    public function can_match_a_string_snapshot()
    {
        $data = 'Foo';

        $this->assertMatchesSnapshot($data);
    }

    /** @test */
    public function can_match_an_xml_snapshot()
    {
        $data = '<foo><bar>Baz</bar></foo>';

        $this->assertMatchesXmlSnapshot($data);
    }

    /** @test */
    public function can_match_a_json_snapshot()
    {
        $data = '{"foo":"foo","bar":"bar","baz":"baz"}';

        $this->assertMatchesJsonSnapshot($data);
    }

    /** @test */
    public function can_match_a_file_hash_snapshot()
    {
        $filePath = __DIR__.'/stubs/example_snapshots/snapshot.json';

        $this->assertMatchesFileHashSnapshot($filePath);
    }

    /** @test */
    public function can_match_a_file_snapshot()
    {
        $filePath = __DIR__.'/stubs/test_files/friendly_man.jpg';

        $this->assertMatchesFileSnapshot($filePath);
    }

    /** @test */
    public function can_do_multiple_snapshot_assertions()
    {
        $this->assertMatchesSnapshot('Foo');
        $this->assertMatchesSnapshot('Bar');
    }
}
