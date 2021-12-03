<?php

namespace Spatie\Snapshots\Drivers;

use Composer\InstalledVersions;
use JetBrains\PhpStorm\ArrayShape;
use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

class ObjectDriver implements Driver
{
    #[ArrayShape(['encoder' => 'string', 'format' => 'string', 'context' => 'array'])]
    public static $config = [
        'encoder' => YamlEncoder::class,
        'format' => YamlEncoder::FORMAT,
        'context' => [
            'yaml_inline' => 2,
            'yaml_indent' => 4,
            'yaml_flags' => Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK,
        ],
    ];

    public function serialize($data): string
    {
        $normalizers = [
            new DateTimeNormalizer(),
            new ObjectNormalizer(),
        ];

        $encoders = [
            new (self::$config['encoder'])(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        if ($data instanceof \stdClass) {
            $serializerVersion = InstalledVersions::getVersion('symfony/serializer');

            if (version_compare($serializerVersion, '5.1.0.0') < 0) {
                // The Symfony serializer (before 5.1 version) doesn't support `stdClass`.
                $data = (array) $data;
            }
        }

        return $this->dedent(
            $serializer->serialize($data, self::$config['format'], self::$config['context'])
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
