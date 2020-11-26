<?php

namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

class ObjectDriver implements Driver
{
    public function serialize($data): string
    {
        $normalizers = [
            new DateTimeNormalizer(),
            new ObjectNormalizer(),
        ];

        $encoders = [
            new YamlEncoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        // The Symfony serialized doesn't support `stdClass` yet.
        // This may be removed when Symfony 5.1 is released.
        if ($data instanceof \stdClass) {
            $data = (array) $data;
        }

        return $this->dedent(
            $serializer->serialize($data, 'yaml', [
                'yaml_inline' => 2,
                'yaml_indent' => 4,
                'yaml_flags' => Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK,
            ])
        );
    }

    public function extension(): string
    {
        return 'yml';
    }

    public function match($expected, $actual)
    {
        Assert::assertEquals($expected, $this->serialize($actual));
    }

    protected function dedent(string $string): string
    {
        return preg_replace('/^ {4}/m', '', $string);
    }
}
