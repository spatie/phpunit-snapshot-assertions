<?php

namespace Spatie\Snapshots\Test\Unit;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Filename;

class FilenameTest extends TestCase
{
    /** @test */
    public function it_creates_a_filename_which_is_valid_on_all_systems()
    {
        $name = 'ClassTest__testOne with... data set "Empty".php';

        $this->assertEquals('ClassTest__testOne with data set Empty.php', Filename::cleanFilename($name));
    }
}
