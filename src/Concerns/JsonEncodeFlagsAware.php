<?php

namespace Spatie\Snapshots\Concerns;

trait JsonEncodeFlagsAware
{
    /*
     * Determines the flags of json_encode() function.
     * @see https://www.php.net/manual/en/json.constants.php
     */
    protected function getJsonEncodeFlags(): int
    {
        return JSON_PRETTY_PRINT;
    }
}
