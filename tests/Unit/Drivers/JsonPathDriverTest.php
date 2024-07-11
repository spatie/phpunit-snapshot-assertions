<?php

declare(strict_types=1);

namespace Spatie\Snapshots\Test\Unit\Drivers;

use Generator;
use JsonPath\JsonObject;
use JsonPath\JsonPath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\Drivers\JsonPathDriver;

class JsonPathDriverTest extends TestCase
{
    #[Test]
    #[DataProvider('provideJsonData')]
    public function it_can_replace_placeholders_in_json(string $pathExpected, string $pathActual, array $replacements): void
    {
        $expected = file_get_contents($pathExpected);
        $actual = file_get_contents($pathActual);
        $driver = new JsonPathDriver($replacements);

        try {
            $driver->match($expected, $actual);
            $status = true;
        } catch (ExpectationFailedException $e) {
            print(PHP_EOL.PHP_EOL.$e->getMessage().PHP_EOL.PHP_EOL);
            $status = false;
        }

        $this->assertTrue($status);
    }

    public static function provideJsonData(): Generator
    {
        yield 'simple' => [
            dirname(__DIR__).'/test_files/json_path_simpleA.json',
            dirname(__DIR__).'/test_files/json_path_simpleB.json',
            [
                '$.id' => '@\d+@',
                '$.cover' => '@https://bucket.foo/bar/\d+.[webp|jpg]@',
                '$.createdAt' => '@\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[\+\-]\d{2}:\d{2}@',
            ]
        ];

        yield 'json:api' => [
            dirname(__DIR__).'/test_files/json_path_jsonapiA.json',
            dirname(__DIR__).'/test_files/json_path_jsonapiB.json',
            [
                '$.data..id' => '@\d+@',
                '$.data..links.*' => '@http://example.com/articles/\d+(/[a-z/]+)?@',
                '$.included..id' => '@\d+@',
                '$.included..links.self' => '@http://example.com/(people|comments)/\d+@',
            ]
        ];
    }
}
