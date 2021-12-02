<?php

namespace Spatie\Snapshots;

class Filename
{
    public static function cleanFilename(string $raw): string
    {
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;[]().
        $file = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $raw);

        // Remove any runs of periods
        $file = preg_replace("([\.]{2,})", '', $file);

        return $file;
    }
}
