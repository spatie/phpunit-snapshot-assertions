<?php

namespace Spatie\Snapshots\Test\Unit\Drivers;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\ImageDriver;

class ImageDriverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->driver = new ImageDriver();

        $this->pathToImageA = __DIR__ . '/../test_files/testA.png';
        $this->pathToImageB = __DIR__ . '/../test_files/testB.png';
        $this->pathToImageWithDifferentDimensions = __DIR__ . '/../test_files/testC.png';

    }

    /** @test */
    public function it_can_serialize_an_image()
    {
        $data = $this->driver->serialize($this->pathToImageA);

        $this->assertEquals($data, file_get_contents($this->pathToImageA));
    }

    /** @test */
    public function it_can_determine_that_two_images_are_the_same()
    {
        $this->driver->match(
            file_get_contents($this->pathToImageA),
            file_get_contents($this->pathToImageA),
        );

        $this->doesNotPerformAssertions();
    }

    /** @test */
    public function it_can_determine_that_two_images_are_not_same()
    {
        $this->expectException(ExpectationFailedException::class);

        $this->driver->match(
            file_get_contents($this->pathToImageA),
            file_get_contents($this->pathToImageB),
        );
    }

    /** @test */
    public function it_will_determine_that_two_images_with_different_dimensions_are_different()
    {
        $this->expectException(ExpectationFailedException::class);

        $this->driver->match(
            file_get_contents($this->pathToImageA),
            file_get_contents($this->pathToImageWithDifferentDimensions),
        );
    }
}
