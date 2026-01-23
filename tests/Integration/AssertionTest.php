<?php

namespace Spatie\Snapshots\Test\Integration;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class AssertionTest extends TestCase
{
    use ComparesSnapshotFiles;
    use MatchesSnapshots;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpComparesSnapshotFiles();
    }

    /** @test */
    #[Test]
    public function can_match_a_string_snapshot()
    {
        $data = 'Foo';

        $this->assertMatchesSnapshot($data);
    }

    /** @test */
    #[Test]
    public function can_match_an_html_snapshot()
    {
        $data = '<!doctype html><html lang="en"><head></head><body><h1>Hello, world!</h1></body></html>';

        $this->assertMatchesHtmlSnapshot($data);
    }

    /** @test */
    #[Test]
    public function can_match_an_xml_snapshot()
    {
        $data = '<foo><bar>Baz</bar></foo>';

        $this->assertMatchesXmlSnapshot($data);
    }

    /** @test */
    #[Test]
    public function can_match_a_json_snapshot()
    {
        $data = '{"foo":"foo","bar":"bar","baz":"baz"}';

        $this->assertMatchesJsonSnapshot($data);
    }

    /** @test */
    #[Test]
    public function can_match_an_array_snapshot()
    {
        $data = ['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz'];

        $this->assertMatchesJsonSnapshot($data);
    }

    /** @test */
    #[Test]
    public function can_match_a_file_hash_snapshot()
    {
        $filePath = __DIR__.'/stubs/example_snapshots/snapshot.json';

        $this->assertMatchesFileHashSnapshot($filePath);
    }

    /** @test */
    #[Test]
    public function can_match_a_file_snapshot()
    {
        $filePath = __DIR__.'/stubs/test_files/friendly_man.jpg';

        $this->assertMatchesFileSnapshot($filePath);
    }

    /** @test */
    #[Test]
    public function can_do_multiple_snapshot_assertions()
    {
        $this->assertMatchesSnapshot('Foo');
        $this->assertMatchesSnapshot('Bar');
    }

    #[Test]
    public function can_match_a_snapshot_with_explicit_id()
    {
        $this->assertMatchesSnapshot('Foo', null, 'my-snapshot');
    }

    #[Test]
    public function can_match_a_json_snapshot_with_explicit_id()
    {
        $this->assertMatchesJsonSnapshot('{"foo":"bar"}', 'my-json-snapshot');
    }

    #[Test]
    public function can_match_an_html_snapshot_with_explicit_id()
    {
        $this->assertMatchesHtmlSnapshot('<html><body>Hello</body></html>', 'my-html-snapshot');
    }

    #[Test]
    public function can_match_an_xml_snapshot_with_explicit_id()
    {
        $this->assertMatchesXmlSnapshot('<root><item>Test</item></root>', 'my-xml-snapshot');
    }

    #[Test]
    public function can_match_a_text_snapshot_with_explicit_id()
    {
        $this->assertMatchesTextSnapshot('Hello World', 'my-text-snapshot');
    }

    #[Test]
    public function can_match_an_object_snapshot_with_explicit_id()
    {
        $this->assertMatchesObjectSnapshot(['key' => 'value'], 'my-object-snapshot');
    }

    #[Test]
    public function can_match_a_yaml_snapshot_with_explicit_id()
    {
        $this->assertMatchesYamlSnapshot(['name' => 'test'], 'my-yaml-snapshot');
    }

    #[Test]
    public function can_match_a_file_hash_snapshot_with_explicit_id()
    {
        $filePath = __DIR__.'/stubs/example_snapshots/snapshot.json';

        $this->assertMatchesFileHashSnapshot($filePath, 'my-file-hash-snapshot');
    }

    #[Test]
    public function can_match_a_file_snapshot_with_explicit_id()
    {
        $filePath = __DIR__.'/stubs/test_files/friendly_man.jpg';

        $this->assertMatchesFileSnapshot($filePath, 'my-file-snapshot');
    }

    #[Test]
    public function explicit_ids_do_not_affect_auto_increment_ids()
    {
        // First auto-increment: __1
        $this->assertMatchesSnapshot('First');

        // Explicit ID: __s-named
        $this->assertMatchesSnapshot('Named', null, 'named');

        // Second auto-increment should be __2, not __3
        $this->assertMatchesSnapshot('Second');

        // Another explicit ID
        $this->assertMatchesSnapshot('AnotherNamed', null, 'another-named');

        // Third auto-increment should be __3
        $this->assertMatchesSnapshot('Third');
    }

    #[Test]
    public function numeric_explicit_ids_do_not_conflict_with_auto_increment_ids()
    {
        // Explicit ID with numeric value that could conflict: __s-1
        $this->assertMatchesSnapshot('ExplicitOne', null, '1');

        // Auto-increment: __1 (should not conflict with __s-1)
        $this->assertMatchesSnapshot('AutoOne');

        // Another explicit numeric ID: __s-2
        $this->assertMatchesSnapshot('ExplicitTwo', null, '2');

        // Auto-increment: __2 (should not conflict with __s-2)
        $this->assertMatchesSnapshot('AutoTwo');
    }
}
