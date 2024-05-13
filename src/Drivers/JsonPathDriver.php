<?php

namespace Spatie\Snapshots\Drivers;

use Exception;
use JsonPath\JsonObject;
use PHPUnit\Framework\Assert;

class JsonPathDriver extends JsonDriver
{
    public function __construct(private readonly array $placeholders = [])
    {
    }

    public function match($expected, $actual): void
    {
        if (! class_exists(JsonObject::class)) {
            throw new Exception('The galbar/jsonpath package is not installed. Please install it to enable JSONPath driver.');
        }

        if (is_string($actual)) {
            $actual = json_decode($actual, false, 512, JSON_THROW_ON_ERROR);
        }

        $expected = json_decode($expected, false, 512, JSON_THROW_ON_ERROR);

        $jpActual = new JsonObject($actual);
        $jpExpected = new JsonObject($expected);
        foreach ($this->placeholders as $path => $pattern) {
            $actualData = $jpActual->getJsonObjects($path);

            if (0 === count($actualData)) {
                Assert::fail('Failed asserting that JSON path "'.$path.'" exists.');
            }

            $expectedData = $jpExpected->getJsonObjects($path);
            foreach ($actualData as $i => $data) {
                Assert::assertMatchesRegularExpression($pattern, $data->getValue(), 'Failed asserting that JSON path "'.$path.'" matches pattern "'.$pattern.'".');
                $data->set('$', $expectedData[$i]->getValue());
            }
        }

        Assert::assertJsonStringEqualsJsonString($jpExpected->getJson(), $jpActual->getJson());
    }
}
