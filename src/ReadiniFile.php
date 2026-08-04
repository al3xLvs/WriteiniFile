<?php

namespace WriteiniFile;

/**
 * Class ReadiniFile.
 */
class ReadiniFile
{
    /**
     * @var string $path_to_ini_file
     */
    protected static $path_to_ini_file;

    /**
     * @var array $data_ini_file
     */
    protected static $data_ini_file;

    /**
     * method for get data of ini file.
     *
     * @param string $ini_file         path of ini file
     * @param int    $scanner_mode     scanner mode INI_SCANNER_RAW INI_SCANNER_TYPED or INI_SCANNER_NORMAL
     * @param bool   $process_sections true to have sections, false otherwise
     *
     * @return array ini file data in a array
     */
    public static function get($ini_file, $scanner_mode = INI_SCANNER_RAW, $process_sections = true)
    {
        self::$path_to_ini_file = $ini_file;

        if (file_exists(self::$path_to_ini_file) === true) {
            self::$data_ini_file = @parse_ini_file(self::$path_to_ini_file, $process_sections, $scanner_mode);
        } else {
            throw new \Exception(sprintf('File ini does not exist: %s', self::$path_to_ini_file));
        }

        if (self::$data_ini_file === false) {
            throw new \Exception(sprintf('Unable to parse file ini: %s', self::$path_to_ini_file));
        }

        return self::$data_ini_file;
    }
}
