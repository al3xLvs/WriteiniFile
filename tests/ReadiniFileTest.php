<?php

declare(strict_types=1);

namespace WriteiniFile\Tests;

use WriteiniFile\IniFileException;
use WriteiniFile\ReadiniFile;

final class ReadiniFileTest extends TestCase
{
    public function testGet(): void
    {
        $this->assertSame(
            [
                'title' => 'Demo',
                'database' => ['host' => 'localhost', 'port' => '3306'],
            ],
            ReadiniFile::get($this->fixture('GlobalsAndSections.ini'))
        );
    }

    public function testGetTyped(): void
    {
        $data = ReadiniFile::get($this->fixture('GlobalsAndSections.ini'), INI_SCANNER_TYPED);

        $this->assertIsArray($data['database']);
        $this->assertSame(3306, $data['database']['port']);
    }

    public function testFileDoesNotExist(): void
    {
        $path = $this->tmpFile('missing.ini');

        $this->expectException(IniFileException::class);
        $this->expectExceptionMessage('File ini does not exist: '.$path);

        ReadiniFile::get($path);
    }

    public function testUnableToParse(): void
    {
        $path = $this->fixture('CorruptiniFile.ini');

        $this->expectException(IniFileException::class);
        $this->expectExceptionMessage('Unable to parse file ini: '.$path);

        ReadiniFile::get($path);
    }
}
