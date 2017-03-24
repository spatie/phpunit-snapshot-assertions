<?php

namespace Spatie\Snapshots\Drivers;

use DOMDocument;
use PHPUnit\Util\Xml;
use Spatie\Snapshots\Driver;
use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class XmlDriver implements Driver
{
    public function serialize($data): string
    {
        if (! is_string($data)) {
            throw new CantBeSerialized('Only strings can be serialized to xml');
        }

        $domDocument = new DOMDocument('1.0');
        $domDocument->preserveWhiteSpace = false;
        $domDocument->formatOutput = true;

        $domDocument->loadXML($data);

        return $domDocument->saveXML();
    }

    public function extension(): string
    {
        return '.xml';
    }

    public function load(string $path)
    {
        return file_get_contents($path);
    }

    public function match($expected, $actual)
    {
        Assert::assertEquals(Xml::load($expected), Xml::load($actual));
    }
}
