<?php

namespace Spatie\Snapshots\Test\Integration;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class AssertionTest extends TestCase
{
    use ComparesSnapshotFiles;
    use MatchesSnapshots;

    public function setUp()
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
    public function can_do_multiple_snapshot_assertions()
    {
        $this->assertMatchesSnapshot('Foo');
        $this->assertMatchesSnapshot('Bar');
    }

    /** @test */
    public function can_set_custom_string_error_message()
    {
        $customMessage = 'custom string error message';

        try {
            $this->assertMatchesSnapshot('Bar', null, $customMessage);
        } catch (ExpectationFailedException $e) {
            $this->assertNotSame(false, strpos($e->getMessage(), $customMessage), 'Failed to find custom string error message');
            return;
        }

        /** Mark test as failed if we don't get a ExpectationFailedException */
        throw new ExpectationFailedException('ExpectationFailedException did not occur');
    }

    /** @test */
    public function can_set_custom_xml_error_message()
    {
        $customMessage = 'custom XML error message';

        try {
            $this->assertMatchesXmlSnapshot('<baz><bar>Foo</bar></baz>', $customMessage);
        } catch (ExpectationFailedException $e) {
            $this->assertNotSame(false, strpos($e->getMessage(), $customMessage), 'Failed to find custom XML error message');
            return;
        }

        /** Mark test as failed if we don't get a ExpectationFailedException */
        throw new ExpectationFailedException('ExpectationFailedException did not occur');
    }

    /** @test */
    public function can_set_custom_json_error_message()
    {
        $customMessage = 'custom JSON error message';

        try {
            $this->assertMatchesJsonSnapshot('{"bar":"bar","foo":"foo"}', $customMessage);
        } catch (ExpectationFailedException $e) {
            $this->assertNotSame(false, strpos($e->getMessage(), $customMessage), 'Failed to find custom JSON error message');
            return;
        }

        /** Mark test as failed if we don't get a ExpectationFailedException */
        throw new ExpectationFailedException('ExpectationFailedException did not occur');
    }
}
