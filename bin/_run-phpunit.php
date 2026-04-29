<?php

/*
 * Locates the PHPUnit binary and runs it. Shared helper used by the
 * `update-snapshots` and `without-creating-snapshots` wrappers, which set
 * the relevant environment variable before requiring this file.
 */

$candidates = [];

if (isset($GLOBALS['_composer_autoload_path'])) {
    $candidates[] = dirname($GLOBALS['_composer_autoload_path']).'/phpunit/phpunit/phpunit';
}

$candidates[] = dirname(__DIR__, 3).'/phpunit/phpunit/phpunit';
$candidates[] = dirname(__DIR__).'/vendor/phpunit/phpunit/phpunit';

foreach ($candidates as $phpunit) {
    if (file_exists($phpunit)) {
        require $phpunit;

        return;
    }
}

fwrite(STDERR, 'Could not locate PHPUnit. Make sure phpunit/phpunit is installed.'.PHP_EOL);
exit(1);
