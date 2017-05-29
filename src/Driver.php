<?php

namespace Spatie\Snapshots;

interface Driver
{
    /**
     * Serialize a snapshot's data to a string that can be written to a
     * generated snapshot file.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function serialize($data): string;

    /**
     * The extension that should be used to save the snapshot file, without
     * a leading dot.
     *
     * @return string
     */
    public function extension(): string;

    /**
     * Match an expectation with a snapshot's actual contents. Should throw an
     * `ExpectationFailedException` if it doesn't match. This happens by
     * default if you're using PHPUnit's `Assert` class for the match.
     *
     * @param mixed $expected
     * @param mixed $actual
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function match($expected, $actual);
}
