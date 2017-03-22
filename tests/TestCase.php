<?php

namespace Spatie\Snapshots\Test;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
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
