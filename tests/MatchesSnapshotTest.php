<?php

namespace Spatie\Snapshots\Test;

use Spatie\Snapshots\MatchesSnapshots;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_ExpectationFailedException;

class MatchesSnapshotTest extends TestCase
{
    /** @test */
    public function it_can_create_a_snapshot_from_a_string()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesSnapshot('Foo');

        $this->filesystem->assertSnapshotMatchesExample('MatchesSnapshotTest__it_can_match_an_existing_string_snapshot.php', 'snapshot.php');
    }

    /** @test */
    public function it_can_create_a_snapshot_from_xml()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Baz</bar></foo>');

        $this->filesystem->assertSnapshotMatchesExample('MatchesSnapshotTest__it_can_create_a_snapshot_from_xml.xml', 'snapshot.xml');
    }

    /** @test */
    public function it_can_create_a_snapshot_from_json()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');

        $this->filesystem->assertSnapshotMatchesExample('MatchesSnapshotTest__it_can_create_a_snapshot_from_json.json', 'snapshot.json');
    }

    /** @test */
    public function it_can_match_an_existing_string_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait
            ->expects($this->once())
            ->method('assertEquals')
            ->with('Foo', 'Foo');

        $mockTrait->assertMatchesSnapshot('Foo');
    }

    /** @test */
    public function it_can_match_an_existing_xml_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait
            ->expects($this->once())
            ->method('assertXmlStringEqualsXmlString')
            ->with($this->stringContains('<bar>Baz</bar>'), '<foo><bar>Baz</bar></foo>');

        $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Baz</bar></foo>');
    }

    /** @test */
    public function it_can_match_an_existing_json_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait
            ->expects($this->once())
            ->method('assertJsonStringEqualsJsonString')
            ->with($this->stringContains('"foo": "foo"'), '{"foo":"foo","bar":"bar","baz":"baz"}');

        $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');
    }

    /** @test */
    public function it_can_mismatch_a_string_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait
            ->expects($this->once())
            ->method('assertEquals')
            ->willThrowException(new PHPUnit_Framework_ExpectationFailedException(''));

        $this->expectException(PHPUnit_Framework_ExpectationFailedException::class);

        $mockTrait->assertMatchesSnapshot('Bar');
    }

    /** @test */
    public function it_can_mismatch_a_xml_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait
            ->expects($this->once())
            ->method('assertXmlStringEqualsXmlString')
            ->willThrowException(new PHPUnit_Framework_ExpectationFailedException(''));

        $this->expectException(PHPUnit_Framework_ExpectationFailedException::class);

        $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Foo</bar></foo>');
    }

    /** @test */
    public function it_can_mismatch_a_json_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait
            ->expects($this->once())
            ->method('assertJsonStringEqualsJsonString')
            ->willThrowException(new PHPUnit_Framework_ExpectationFailedException(''));

        $this->expectException(PHPUnit_Framework_ExpectationFailedException::class);

        $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');
    }

    /** @test */
    public function it_can_update_a_string_snapshot()
    {
        $_SERVER['argv']['test'] = '--update';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesSnapshot('Foo');

        $this->filesystem->assertSnapshotMatchesExample('MatchesSnapshotTest__it_can_update_a_string_snapshot.php', 'snapshot.php');
    }

    /** @test */
    public function it_can_update_a_xml_snapshot()
    {
        $_SERVER['argv'][] = '--update';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Baz</bar></foo>');

        $this->filesystem->assertSnapshotMatchesExample('MatchesSnapshotTest__it_can_update_a_xml_snapshot.xml', 'snapshot.xml');
    }

    /** @test */
    public function it_can_update_a_json_snapshot()
    {
        $_SERVER['argv'][] = '--update';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');

        $this->filesystem->assertSnapshotMatchesExample('MatchesSnapshotTest__it_can_update_a_json_snapshot.json', 'snapshot.json');
    }

    protected function expectIncompleteMatchesSnapshotTest(PHPUnit_Framework_MockObject_MockObject $matchesSnapshotMock)
    {
        $matchesSnapshotMock
            ->expects($this->atMost(1))
            ->method('assertEquals')
            ->willThrowException(new PHPUnit_Framework_ExpectationFailedException(''));

        $matchesSnapshotMock
            ->expects($this->atMost(1))
            ->method('assertXmlStringEqualsXmlString')
            ->willThrowException(new PHPUnit_Framework_ExpectationFailedException(''));

        $matchesSnapshotMock
            ->expects($this->atMost(1))
            ->method('assertJsonStringEqualsJsonString')
            ->willThrowException(new PHPUnit_Framework_ExpectationFailedException(''));

        $matchesSnapshotMock
            ->expects($this->once())
            ->method('markTestIncomplete');
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMatchesSnapshotMock(): PHPUnit_Framework_MockObject_MockObject
    {
        $mockMethods = [
            'assertEquals',
            'markTestIncomplete',
            'assertXmlStringEqualsXmlString',
            'assertJsonStringEqualsJsonString',
        ];

        return $this->getMockForTrait(
            MatchesSnapshots::class,
            [], '', true, true, true,
            $mockMethods
        );
    }
}
