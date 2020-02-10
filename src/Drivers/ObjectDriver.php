<?php

namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ObjectDriver implements Driver
{
    public function serialize($data): string
    {
        $normalizers = [
            new DateTimeNormalizer(),
            new ObjectNormalizer(),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        // The Symfony serialized doesn't support `stdClass` yet.
        // This may be removed when Symfony 5.1 is released.
        if ($data instanceof \stdClass) {
            $data = (array) $data;
        }

        return $serializer->serialize(
            $data,
            'json',
            [
                'json_encode_options' => JSON_PRETTY_PRINT,
            ]
        );
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        Assert::assertJsonStringEqualsJsonString($expected, $this->serialize($actual));
    }
}
