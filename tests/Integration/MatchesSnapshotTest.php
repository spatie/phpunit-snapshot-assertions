<?php

namespace Spatie\Snapshots\Test\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Spatie\Snapshots\MatchesSnapshots;

class MatchesSnapshotTest extends TestCase
{
    use ComparesSnapshotFiles;

    public function setUp()
    {
        parent::setUp();

        $this->setUpComparesSnapshotFiles();

        $updateArgument = array_search('--update-snapshots', $_SERVER['argv']);

        if ($updateArgument) {
            unset($_SERVER['argv'][$updateArgument]);
        }
    }

    /** @test */
    public function it_can_create_a_snapshot_from_a_string()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesSnapshot('Foo');

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_match_an_existing_string_snapshot__1.php',
            'snapshot.php'
        );
    }

    /** @test */
    public function it_can_create_a_snapshot_from_xml()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Baz</bar></foo>');

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_create_a_snapshot_from_xml__1.xml',
            'snapshot.xml'
        );
    }

    /** @test */
    public function it_can_create_a_snapshot_from_json()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_create_a_snapshot_from_json__1.json',
            'snapshot.json'
        );
    }

    /** @test */
    public function it_can_match_an_existing_string_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait->assertMatchesSnapshot('Foo');
    }

    /** @test */
    public function it_can_match_an_existing_xml_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Baz</bar></foo>');
    }

    /** @test */
    public function it_can_match_an_existing_json_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');
    }

    /** @test */
    public function it_can_mismatch_a_string_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFailedMatchesSnapshotTest();

        $mockTrait->assertMatchesSnapshot('Bar');
    }

    /** @test */
    public function it_can_mismatch_a_xml_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFailedMatchesSnapshotTest();

        $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Foo</bar></foo>');
    }

    /** @test */
    public function it_can_mismatch_a_json_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFailedMatchesSnapshotTest();

        $mockTrait->assertMatchesJsonSnapshot('{"foo":"baz","bar":"baz","baz":"foo"}');
    }

    /** @test */
    public function it_can_update_a_string_snapshot()
    {
        $_SERVER['argv'][] = '--update-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesSnapshot('Foo');

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_update_a_string_snapshot__1.php',
            'snapshot.php'
        );
    }

    /** @test */
    public function it_can_update_a_xml_snapshot()
    {
        $_SERVER['argv'][] = '--update-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Baz</bar></foo>');

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_update_a_xml_snapshot__1.xml',
            'snapshot.xml'
        );
    }

    /** @test */
    public function it_can_update_a_json_snapshot()
    {
        $_SERVER['argv'][] = '--update-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_update_a_json_snapshot__1.json',
            'snapshot.json'
        );
    }

    private function expectIncompleteMatchesSnapshotTest(PHPUnit_Framework_MockObject_MockObject $matchesSnapshotMock)
    {
        $matchesSnapshotMock
            ->expects($this->once())
            ->method('markTestIncomplete');
    }

    private function expectFailedMatchesSnapshotTest()
    {
        if (class_exists('PHPUnit\Framework\ExpectationFailedException')) {
            $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        } else {
            $this->expectException('PHPUnit_Framework_ExpectationFailedException');
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMatchesSnapshotMock()
    {
        $mockMethods = [
            'markTestIncomplete',
            'getSnapshotId',
            'getSnapshotDirectory',
        ];

        $matchesSnapshotMock = $this->getMockForTrait(
            MatchesSnapshots::class,
            [], '', true, true, true,
            $mockMethods
        );

        $matchesSnapshotMock
            ->expects($this->any())
            ->method('getSnapshotId')
            ->willReturn('MatchesSnapshotTest__'.$this->getName().'__1');

        $matchesSnapshotMock
            ->expects($this->any())
            ->method('getSnapshotDirectory')
            ->willReturn(__DIR__.'/__snapshots__');

        return $matchesSnapshotMock;
    }
}
