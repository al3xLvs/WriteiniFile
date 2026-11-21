<?php

declare(strict_types=1);

namespace WriteiniFile;

/**
 * Reads an ini file into an array.
 */
final class ReadiniFile
{
    /**
     * @param string $path            Path of the ini file
     * @param int    $scannerMode     INI_SCANNER_RAW, INI_SCANNER_TYPED or INI_SCANNER_NORMAL
     * @param bool   $processSections true to group the values by section, false for an ini file without sections
     *
     * @return array<array-key, mixed>
     *
     * @throws IniFileException if the file does not exist or cannot be parsed
     */
    public static function get(string $path, int $scannerMode = INI_SCANNER_RAW, bool $processSections = true): array
    {
        if (!is_file($path)) {
            throw IniFileException::fileNotFound($path);
        }

        $data = @parse_ini_file($path, $processSections, $scannerMode);

        if ($data === false) {
            throw IniFileException::unableToParse($path);
        }

        return $data;
    }
}
