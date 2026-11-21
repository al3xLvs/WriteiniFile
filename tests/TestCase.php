<?php

declare(strict_types=1);

namespace WriteiniFile\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected const FIXTURES = __DIR__.'/file_ini';

    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir().'/writeinifile-'.bin2hex(random_bytes(6));
        mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir.'/*') ?: []);
        rmdir($this->tmpDir);
    }

    /**
     * Path of a file in a temporary directory, optionally initialized with a copy of a fixture.
     */
    protected function tmpFile(string $name, ?string $copyOf = null): string
    {
        $path = $this->tmpDir.'/'.$name;

        if ($copyOf !== null) {
            copy(self::FIXTURES.'/'.$copyOf, $path);
        }

        return $path;
    }

    protected function fixture(string $name): string
    {
        return self::FIXTURES.'/'.$name;
    }
}
