<?php

namespace Spatie\Snapshots\Test;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var  Filesystem */
    protected $filesystem;

    public function setUp()
    {
        parent::setUp();

        $this->filesystem = new Filesystem();

        $this->filesystem->prepareSnapshots();

        $_SERVER['argv']['test'] = null;
    }
}
