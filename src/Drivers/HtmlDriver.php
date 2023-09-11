<?php

namespace Spatie\Snapshots\Drivers;

use DOMDocument;
use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class HtmlDriver implements Driver
{
    public function serialize($data): string
    {
        if (! is_string($data)) {
            throw new CantBeSerialized('Only strings can be serialized to html');
        }

        if ($data === '') {
            return "\n";
        }

        $domDocument = new DOMDocument('1.0');
        $domDocument->preserveWhiteSpace = false;
        $domDocument->formatOutput = true;

        @$domDocument->loadHTML($data, LIBXML_HTML_NODEFDTD); // to ignore HTML5 errors

        $htmlValue = $domDocument->saveHTML();

        // Normalize line endings for cross-platform tests.
        if (PHP_OS_FAMILY === 'Windows') {
            $htmlValue = implode("\n", explode("\r\n", $htmlValue));
        }

        return $htmlValue;
    }

    public function extension(): string
    {
        return 'html';
    }

    public function match($expected, $actual)
    {
        Assert::assertEquals($expected, $this->serialize($actual));
    }
}
