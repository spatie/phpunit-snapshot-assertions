<?php

namespace Spatie\Snapshots\Concerns;

use PHPUnit\Framework\TestCase;

if (method_exists(TestCase::class, 'name')) {
    trait PhpUnitCompatibility
    {
    }
} else {
    trait PhpUnitCompatibility
    {
        public function name(): string
        {
            return $this->getName(false);
        }

        public function nameWithDataSet(): string
        {
            return $this->getName(true);
        }
    }
}
