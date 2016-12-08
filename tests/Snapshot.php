<?php

namespace Spatie\Snapshots\Test;

use DOMDocument;
use ReflectionClass;

class Snapshot
{
    /** @var string */
    protected $directory, $id, $type;

    private function __construct(string $directory, string $id, string $type)
    {
        $this->directory = $directory;
        $this->id = $id;
        $this->type = $type;
    }

    public static function forTestMethod($backtrace, $type): self
    {
        $class = new ReflectionClass($backtrace['class']);
        $method = $backtrace['function'];

        $directory = dirname($class->getFileName()).'/__snapshots__';
        $id = "{$class->getShortName()}__{$method}";

        return new self($directory, $id, $type);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function path(): string
    {
        $extension = $this->type === 'var' ? 'php' : $this->type;

        return "{$this->directory}/{$this->id}.{$extension}";
    }

    public function exists(): bool
    {
        return file_exists($this->path());
    }

    public function get()
    {
        if ($this->type === 'var') {
            return include $this->path();
        }

        return file_get_contents($this->path());
    }

    public function create($actual)
    {
        if (! file_exists($this->directory)) {
            mkdir($this->directory);
        }

        file_put_contents($this->path(), $this->serializeForSnapshot($actual));
    }

    protected function serializeForSnapshot($data): string
    {
        if ($this->type === 'xml') {
            return $this->formatXml($data);
        }

        if ($this->type === 'json') {
            return $this->formatJson($data);
        }

        return '<?php return '.var_export($data, true).';';
    }

    protected function formatXml(string $xml): string
    {
        $domDocument = new DOMDocument('1.0');
        $domDocument->preserveWhiteSpace = false;
        $domDocument->formatOutput = true;

        $domDocument->loadXML($xml);

        return $domDocument->saveXML();
    }

    protected function formatJson(string $json): string
    {
        return json_encode(json_decode($json), JSON_PRETTY_PRINT);
    }
}
