<?php

declare(strict_types=1);

namespace WriteiniFile;

/**
 * Thrown when an ini file cannot be read, parsed or written.
 */
class IniFileException extends \RuntimeException
{
    public static function fileNotFound(string $path): self
    {
        return new self(sprintf('File ini does not exist: %s', $path));
    }

    public static function unableToParse(string $path): self
    {
        return new self(sprintf('Unable to parse file ini: %s', $path));
    }

    public static function unableToWrite(string $path): self
    {
        return new self(sprintf('Unable to write in the file ini: %s', $path));
    }

    public static function unsupportedValue(string $key): self
    {
        return new self(sprintf(
            'Unable to write the value of "%s": ini files only support scalar values and one level of arrays.',
            $key
        ));
    }
}
