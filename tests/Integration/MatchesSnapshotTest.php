<?php

namespace Spatie\Snapshots\Test\Integration;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class MatchesSnapshotTest extends TestCase
{
    use ComparesSnapshotFiles;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpComparesSnapshotFiles();

        $updateArgument = array_search('--update-snapshots', $_SERVER['argv']);
        $noCreateArgument = array_search('--no-create-snapshots', $_SERVER['argv']);

        if ($updateArgument) {
            unset($_SERVER['argv'][$updateArgument]);
        }

        if ($noCreateArgument) {
            unset($_SERVER['argv'][$noCreateArgument]);
        }
    }

    /** @test */
    public function it_can_create_a_snapshot_from_a_string()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesSnapshot('Foo');
        });

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_create_a_snapshot_from_a_string__1.txt',
            'string_snapshot.txt'
        );
    }

    /** @test */
    public function it_can_create_a_snapshot_from_an_array()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesSnapshot(['foo' => 'bar']);
        });

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_create_a_snapshot_from_an_array__1.yml',
            'snapshot.yml'
        );
    }

    /** @test */
    public function it_can_create_a_snapshot_from_html()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesHtmlSnapshot('<!doctype html><html lang="en"><head></head><body><h1>Hello, world!</h1></body></html>');
        });

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_create_a_snapshot_from_html__1.html',
            'snapshot.html'
        );
    }

    /** @test */
    public function it_can_create_a_snapshot_from_xml()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Baz</bar></foo>');
        });

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_create_a_snapshot_from_xml__1.xml',
            'snapshot.xml'
        );
    }

    /** @test */
    public function it_can_create_a_snapshot_from_json()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');
        });

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_create_a_snapshot_from_json__1.json',
            'snapshot.json'
        );
    }

    /** @test */
    public function it_can_create_a_snapshot_from_a_file()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/friendly_man.jpg');
        });

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
    public function it_can_match_an_existing_html_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $mockTrait->assertMatchesHtmlSnapshot('<!doctype html><html lang="en"><head></head><body><h1>Hello, world!</h1></body></html>');
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
    public function it_can_mismatch_a_html_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFailedMatchesSnapshotTest();

        $mockTrait->assertMatchesHtmlSnapshot('<!doctype html><html lang="en"><head></head><body><h1>Hallo welt!</h1></body></html>');
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

        $this->expectFail(
            $mockTrait,
            'File did not match snapshot (MatchesSnapshotTest__it_can_mismatch_a_file_snapshot__1.jpg)'
        );

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/troubled_man.jpg');
    }

    /** @test */
    public function it_can_mismatch_a_file_snapshot_with_a_different_extension()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFail(
            $mockTrait,
            'File did not match the snapshot file extension (expected: jpg, was: png)'
        );

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/no_man.png');
    }

    /** @test */
    public function it_needs_a_file_extension_to_do_a_file_snapshot_assertion()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $filePath = __DIR__.'/stubs/test_files/file_without_extension';

        $this->expectFail(
            $mockTrait,
            'Unable to make a file snapshot, file does not have a file extension ' .
            "($filePath)"
        );


        $this->assertFileExists($filePath);

        $mockTrait->assertMatchesFileSnapshot($filePath);
    }

    /** @test */
    public function it_persists_the_failed_file_after_mismatching_a_file_snapshot()
    {
        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFail(
            $mockTrait,
            'File did not match snapshot (MatchesSnapshotTest__it_persists_the_failed_file_after_mismatching_a_file_snapshot__1.jpg)'
        );

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

        $this->assertFileDoesNotExist($persistedFailedFile);
    }

    /** @test */
    public function it_can_update_a_string_snapshot()
    {
        $_SERVER['argv'][] = '--update-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesSnapshot('Foo');
        });

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_update_a_string_snapshot__1.txt',
            'string_snapshot.txt'
        );
    }

    /** @test */
    public function it_can_update_a_html_snapshot()
    {
        $_SERVER['argv'][] = '--update-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesHtmlSnapshot('<!doctype html><html lang="en"><head></head><body><h1>Hello, world!</h1></body></html>');
        });

        $this->assertSnapshotMatchesExample(
            'MatchesSnapshotTest__it_can_update_a_html_snapshot__1.html',
            'snapshot.html'
        );
    }

    /** @test */
    public function it_can_update_a_xml_snapshot()
    {
        $_SERVER['argv'][] = '--update-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesXmlSnapshot('<foo><bar>Baz</bar></foo>');
        });

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

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesJsonSnapshot('{"foo":"foo","bar":"bar","baz":"baz"}');
        });

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

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/friendly_man.jpg');
        });

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

        $oldSnapshot = __DIR__.'/__snapshots__/files/MatchesSnapshotTest__it_can_update_a_file_snapshot_with_a_different_extension__1.jpg';

        $this->assertFileExists($oldSnapshot);

        $this->expectIncompleteMatchesSnapshotTest($mockTrait, function ($mockTrait) {
            $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/no_man.png');
        });

        $this->assertSnapshotMatchesExample(
            'files/MatchesSnapshotTest__it_can_update_a_file_snapshot_with_a_different_extension__1.png',
            'file.png'
        );

        $this->assertFileDoesNotExist($oldSnapshot);
    }

    private function expectIncompleteMatchesSnapshotTest(MockObject $matchesSnapshotMock, callable $assertions)
    {
        $matchesSnapshotMock
            ->expects($this->once())
            ->method('markTestIncomplete');

        $assertions($matchesSnapshotMock);

        $matchesSnapshotMock->markTestIncompleteIfSnapshotsHaveChanged();
    }

    private function expectFail(MockObject $matchesSnapshotMock, string $message)
    {
        $this->expectException(AssertionFailedError::class);

        $matchesSnapshotMock
            ->expects($this->once())
            ->method('fail')
            ->with($message)
            ->willThrowException(new AssertionFailedError());
    }

    private function expectFailedMatchesSnapshotTest()
    {
        $this->expectException(ExpectationFailedException::class);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spatie\Snapshots\MatchesSnapshots
     */
    private function getMatchesSnapshotMock(): MockObject
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


    /** @test */
    public function it_doesnt_create_a_regular_snapshot_and_mismatches_if_asked()
    {
        $_SERVER['argv'][] = '--no-create-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFail(
            $mockTrait,
            "Snapshot \"MatchesSnapshotTest__it_doesnt_create_a_regular_snapshot_and_mismatches_if_asked__1.txt\" does not exist.\n" .
            'You can automatically create it by removing `-d --no-create-snapshots` of PHPUnit\'s CLI arguments.'
        );

        $mockTrait->assertMatchesSnapshot('Bar');
    }

    /** @test */
    public function it_doesnt_create_a_file_snapshot_and_mismatches_if_asked()
    {
        $_SERVER['argv'][] = '--no-create-snapshots';

        $mockTrait = $this->getMatchesSnapshotMock();

        $this->expectFail(
            $mockTrait,
            "Snapshot \"MatchesSnapshotTest__it_doesnt_create_a_file_snapshot_and_mismatches_if_asked__1.jpg_failed.jpg\" does not exist.\n" .
            'You can automatically create it by removing `-d --no-create-snapshots` of PHPUnit\'s CLI arguments.'
        );

        $mockTrait->assertMatchesFileSnapshot(__DIR__.'/stubs/test_files/friendly_man.jpg');
    }
}
