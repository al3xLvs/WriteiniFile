<?php

declare(strict_types=1);

namespace WriteiniFile\Tests;

use WriteiniFile\IniFileException;
use WriteiniFile\ReadiniFile;
use WriteiniFile\WriteiniFile;

final class WriteiniFileTest extends TestCase
{
    private const DATA = [
        'section 1' => [
            'foo' => 'string',
            'foo_string' => 'string',
            'bool_true' => true,
            'bool_false' => false,
            'int_true' => 1,
            'int_false' => 0,
            'int' => 10,
            'float' => 10.3,
            'foo_array' => [
                'string',
                10.3,
                true,
                false,
            ],
        ],
        'section 2' => [
            'foo' => 'string',
            'var' => 'string string',
            'bool_true' => true,
            'bool_false' => false,
            'var_null' => null,
            'int_true_string' => '1',
            'int_false_string' => '0',
            'float_string' => '10.5L',
            'empty_string' => '',
        ],
    ];

    public function testCreate(): void
    {
        $path = $this->tmpFile('test.ini');

        $this->assertTrue((new WriteiniFile($path))->create(self::DATA)->write());

        $this->assertFileEquals($this->fixture('Create.ini'), $path);
    }

    public function testUpdate(): void
    {
        $path = $this->tmpFile('test.ini');

        (new WriteiniFile($path))
            ->create(self::DATA)
            ->update(['section 1' => ['foo' => 'bar', 'int' => 100]])
            ->write();

        $this->assertFileEquals($this->fixture('Update.ini'), $path);
    }

    public function testRm(): void
    {
        $path = $this->tmpFile('test.ini');

        (new WriteiniFile($path))
            ->create(self::DATA)
            ->rm(['section 1' => ['foo' => 'string', 'int' => 10]])
            ->write();

        $this->assertFileEquals($this->fixture('Rm.ini'), $path);
    }

    public function testErase(): void
    {
        $path = $this->tmpFile('test.ini');

        (new WriteiniFile($path))->create(self::DATA)->write();
        (new WriteiniFile($path))->erase()->write();

        $this->assertFileEquals($this->fixture('Erase.ini'), $path);
    }

    public function testAdd(): void
    {
        $path = $this->tmpFile('test.ini');

        (new WriteiniFile($path))
            ->create(self::DATA)
            ->add(['section 3' => ['foo' => 'bar', 'var_float' => 10.5]])
            ->write();

        $this->assertFileEquals($this->fixture('Add.ini'), $path);
    }

    public function testEscapeCharacters(): void
    {
        $path = $this->tmpFile('test.ini');

        (new WriteiniFile($path))
            ->create([
                'section 1' => [
                    'foo' => '/usr/bin/example --name="Greg\'s test" --output=./dist/',
                    'bar' => 'Exclamation!question?period.',
                ],
            ])
            ->write();

        $this->assertFileEquals($this->fixture('EscapeCharacters.ini'), $path);
    }

    public function testUnchangedData(): void
    {
        $path = $this->tmpFile('test.ini');

        (new WriteiniFile($path))
            ->create(ReadiniFile::get($this->fixture('UnchangeData.ini')))
            ->write();

        $this->assertFileEquals($this->fixture('UnchangeData.ini'), $path);
    }

    public function testGlobalKeysAreWrittenBeforeSections(): void
    {
        $path = $this->tmpFile('test.ini', 'GlobalsAndSections.ini');

        (new WriteiniFile($path))->add(['debug' => 'true'])->write();

        $this->assertFileEquals($this->fixture('GlobalsAndSectionsAdd.ini'), $path);
    }

    public function testWithoutSections(): void
    {
        $path = $this->tmpFile('test.ini', 'NoSections.ini');

        (new WriteiniFile($path, processSections: false))
            ->update(['debug' => true, 'version' => 2])
            ->write();

        $this->assertFileEquals($this->fixture('NoSectionsUpdate.ini'), $path);
    }

    public function testWithoutSectionsRoundTrip(): void
    {
        $path = $this->tmpFile('test.ini', 'NoSections.ini');

        (new WriteiniFile($path, processSections: false))->write();

        $this->assertFileEquals($this->fixture('NoSections.ini'), $path);
    }

    public function testParseCorruptFile(): void
    {
        $path = $this->fixture('CorruptiniFile.ini');

        $this->expectException(IniFileException::class);
        $this->expectExceptionMessage('Unable to parse file ini: '.$path);

        new WriteiniFile($path);
    }

    public function testUnableToWrite(): void
    {
        $path = $this->tmpFile('missing-directory/test.ini');
        $writer = (new WriteiniFile($path))->create(['section 1' => ['foo' => 'string']]);

        $this->expectException(IniFileException::class);
        $this->expectExceptionMessage('Unable to write in the file ini: '.$path);

        $writer->write();
    }
}
