<?php

namespace Tsterker\Tests\Flextable;

use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

// abstract class TestCase extends AbstractTestBenchTestCase
abstract class TestCase extends AbstractPackageTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->withFactories(__DIR__.'/stubs/factories');
    }
}
