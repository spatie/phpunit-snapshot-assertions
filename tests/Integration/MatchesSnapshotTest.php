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
    public function it_can_create_a_plaintext_snapshot_from_a_string()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesPlaintextSnapshot('Foo');

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_create_a_plaintext_snapshot_from_a_string__1.txt',
            'snapshot.txt'
        );
    }

    /** @test */
    public function it_can_create_a_snapshot_from_a_file()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/friendly_man.jpg');

        $this->assertSnapshotMatchesExample(
            'files/MatchesSnapshotTest__it_can_create_a_snapshot_from_a_file__1.jpg',
            'file.jpg'
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
    public function it_can_match_an_existing_file_hash_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait->assertMatchesFileHashSnapshot(__DIR__.'/stubs/example_snapshots/snapshot.json');
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
    public function it_can_mismatch_a_file_hash_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFailedMatchesSnapshotTest();

        $mockTrait->assertMatchesFileHashSnapshot(__DIR__.'/stubs/example_snapshots/snapshot.json');
    }

    /** @test */
    public function it_can_mismatch_a_file_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFail($mockTrait);

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/troubled_man.jpg');
    }

    /** @test */
    public function it_can_mismatch_a_file_snapshot_with_a_different_extension()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFail($mockTrait);

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/no_man.png');
    }

    /** @test */
    public function it_needs_a_file_extension_to_do_a_file_snapshot_assertion()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFail($mockTrait);

        $filePath = __DIR__.'/stubs/test_files/file_without_extension';

        $this->assertFileExists($filePath);

        $mockTrait->assertMatchesFileSnapshot($filePath);
    }

    /** @test */
    public function it_persists_the_failed_file_after_mismatching_a_file_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFail($mockTrait);

        $mismatchedFile = __DIR__.'/stubs/test_files/troubled_man.jpg';

        $mockTrait->assertMatchesFileSnapshot($mismatchedFile);

        $persistedFailedFile = __DIR__.'/__snapshots__/files/MatchesSnapshotTest__it_persists_the_failed_file_after_mismatching_a_file_snapshot__1.jpg_failed.jpg';

        $this->assertFileExists($persistedFailedFile);
        $this->assertFileEquals($mismatchedFile, $persistedFailedFile);
    }

    /** @test */
    public function it_deletes_the_persisted_failed_file_before_a_file_snapshot_assertion()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait
            ->expects($this->once())
            ->method('assertTrue');

        $persistedFailedFile = __DIR__.'/__snapshots__/files/MatchesSnapshotTest__it_deletes_the_persisted_failed_file_before_a_file_snapshot_assertion__1.jpg_failed.jpg';

        $this->assertTrue(touch($persistedFailedFile));

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/friendly_man.jpg');

        $this->assertFileNotExists($persistedFailedFile);
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

    /** @test */
    public function it_can_update_a_file_snapshot()
    {
        $_SERVER['argv'][] = '--update-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/friendly_man.jpg');

        $this->assertSnapshotMatchesExample(
            'files/MatchesSnapshotTest__it_can_update_a_file_snapshot__1.jpg',
            'file.jpg'
        );
    }

    /** @test */
    public function it_can_update_a_file_snapshot_with_a_different_extension()
    {
        $_SERVER['argv'][] = '--update-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait);

        $oldSnapshot = __DIR__.'/__snapshots__/files/MatchesSnapshotTest__it_can_update_a_file_snapshot_with_a_different_extension__1.jpg';

        $this->assertFileExists($oldSnapshot);

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/no_man.png');

        $this->assertSnapshotMatchesExample(
            'files/MatchesSnapshotTest__it_can_update_a_file_snapshot_with_a_different_extension__1.png',
            'file.png'
        );

        $this->assertFileNotExists($oldSnapshot);
    }

    private function expectIncompleteMatchesSnapshotTest(PHPUnit_Framework_MockObject_MockObject $matchesSnapshotMock)
    {
        $matchesSnapshotMock
            ->expects($this->once())
            ->method('markTestIncomplete');
    }

    private function expectFail(PHPUnit_Framework_MockObject_MockObject $matchesSnapshotMock)
    {
        $matchesSnapshotMock
            ->expects($this->once())
            ->method('fail');
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
    private function getMatchesSnapshotMock(): PHPUnit_Framework_MockObject_MockObject
    {
        $mockMethods = [
            'markTestIncomplete',
            'getSnapshotId',
            'getSnapshotDirectory',
            'getFileSnapshotDirectory',
            'fail',
            'assertTrue',
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

        $matchesSnapshotMock
            ->expects($this->any())
            ->method('getFileSnapshotDirectory')
            ->willReturn(__DIR__.'/__snapshots__/files');

        return $matchesSnapshotMock;
    }
}
