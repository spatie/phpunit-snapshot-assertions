<?php

namespace Spatie\Snapshots\Test\Integration;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UpdateSnapshotsBinaryTest extends TestCase
{
    private string $binary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->binary = dirname(__DIR__, 2).'/bin/update-snapshots';
    }

    #[Test]
    public function it_makes_update_snapshots_visible_to_test_code(): void
    {
        $output = $this->runBinaryWithFixture('shows-env-var');

        $this->assertSame(0, $output['exitCode'], $output['combined']);
    }

    #[Test]
    public function it_restores_a_previously_unset_env_var_on_shutdown(): void
    {
        $output = $this->runBinaryWithFixture('restores-unset', envBefore: null);

        $this->assertSame(0, $output['exitCode'], $output['combined']);
    }

    #[Test]
    public function it_restores_a_previously_set_env_var_on_shutdown(): void
    {
        $output = $this->runBinaryWithFixture('restores-set', envBefore: 'previous-value');

        $this->assertSame(0, $output['exitCode'], $output['combined']);
    }

    /**
     * @return array{exitCode: int, combined: string}
     */
    private function runBinaryWithFixture(string $fixture, ?string $envBefore = null): array
    {
        $fixturePath = $this->writeFixture($fixture);

        $env = array_filter($_SERVER, 'is_string');
        if ($envBefore === null) {
            unset($env['UPDATE_SNAPSHOTS']);
        } else {
            $env['UPDATE_SNAPSHOTS'] = $envBefore;
        }

        $process = proc_open(
            [PHP_BINARY, $this->binary, '--no-configuration', '--bootstrap', dirname(__DIR__, 2).'/vendor/autoload.php', $fixturePath],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            null,
            $env,
        );

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        @unlink($fixturePath);

        return ['exitCode' => $exitCode, 'combined' => $stdout."\n".$stderr];
    }

    private function writeFixture(string $name): string
    {
        $body = match ($name) {
            'shows-env-var' => '$this->assertSame("true", getenv("UPDATE_SNAPSHOTS"));
                $this->assertSame("true", $_ENV["UPDATE_SNAPSHOTS"] ?? null);
                $this->assertSame("true", $_SERVER["UPDATE_SNAPSHOTS"] ?? null);',
            'restores-unset' => 'register_shutdown_function(function () {
                    if (getenv("UPDATE_SNAPSHOTS") !== false || isset($_ENV["UPDATE_SNAPSHOTS"]) || isset($_SERVER["UPDATE_SNAPSHOTS"])) {
                        fwrite(STDERR, "env var not restored to unset state\n");
                        exit(2);
                    }
                });
                $this->assertSame("true", getenv("UPDATE_SNAPSHOTS"));',
            'restores-set' => 'register_shutdown_function(function () {
                    if (getenv("UPDATE_SNAPSHOTS") !== "previous-value") {
                        fwrite(STDERR, "getenv not restored: ".var_export(getenv("UPDATE_SNAPSHOTS"), true)."\n");
                        exit(2);
                    }
                });
                $this->assertSame("true", getenv("UPDATE_SNAPSHOTS"));',
        };

        $className = 'BinaryFixtureTest_'.bin2hex(random_bytes(8));

        $contents = <<<PHP
            <?php
            use PHPUnit\Framework\TestCase;
            class $className extends TestCase {
                public function testIt(): void
                {
                    $body
                }
            }
            PHP;

        $path = sys_get_temp_dir().'/'.$className.'.php';
        file_put_contents($path, $contents);

        return $path;
    }
}
