<?php

namespace Spatie\Snapshots\Test\Unit;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Filename;

class FilenameTest extends TestCase
{
    /**
     * @test
     * @dataProvider fileNameProvider
     */
    public function it_creates_a_filename_which_is_valid_on_all_systems($name, $expected)
    {
        $this->assertEquals($expected, Filename::cleanFilename($name));
    }

    public function fileNameProvider()
    {
        return [
            ['ClassTest__testOne with... data set "Empty".php', 'ClassTest__testOne with data set Empty.php'],
            ['ClassTest__testOne with... data set "空".php', 'ClassTest__testOne with data set 空.php'],
        ];
    }
}
