<?php

declare(strict_types=1);

namespace WriteiniFile;

/**
 * Creates or edits an ini file.
 */
class WriteiniFile
{
    /**
     * @var array<array-key, mixed>
     */
    private array $data;

    /**
     * @param string $path        Path of the ini file. It is created on write() if it does not exist.
     * @param int    $scannerMode INI_SCANNER_RAW, INI_SCANNER_TYPED or INI_SCANNER_NORMAL
     *
     * @throws IniFileException if the file exists but cannot be parsed
     */
    public function __construct(
        private readonly string $path,
        int $scannerMode = INI_SCANNER_RAW,
    ) {
        $this->data = file_exists($path) ? ReadiniFile::get($path, $scannerMode) : [];
    }

    /**
     * Replaces the whole content of the ini file.
     *
     * @param array<array-key, mixed> $data
     */
    public function create(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Changes existing values and adds the missing ones.
     *
     * @param array<array-key, mixed> $data
     */
    public function update(array $data): static
    {
        $this->data = array_replace_recursive($this->data, $data);

        return $this;
    }

    /**
     * Adds values. A key that already exists keeps its value and gets the new one too, as an array.
     *
     * @param array<array-key, mixed> $data
     */
    public function add(array $data): static
    {
        $this->data = array_merge_recursive($this->data, $data);

        return $this;
    }

    /**
     * Removes the given keys, whatever their values. A section is removed when it becomes empty.
     *
     * @param array<array-key, mixed> $data
     */
    public function rm(array $data): static
    {
        $this->data = self::arrayDiffRecursive($this->data, $data);

        return $this;
    }

    /**
     * Removes all the content of the ini file.
     */
    public function erase(): static
    {
        $this->data = [];

        return $this;
    }

    /**
     * Writes the data to the ini file.
     *
     * @throws IniFileException if the file cannot be written
     */
    public function write(): bool
    {
        $content = $this->render();

        if (@file_put_contents($this->path, $content) === false) {
            throw IniFileException::unableToWrite($this->path);
        }

        return true;
    }

    private function render(): string
    {
        $globals = [];
        $sections = [];

        foreach ($this->data as $key => $value) {
            if (is_array($value)) {
                $sections[$key] = $value;
            } else {
                $globals[$key] = $value;
            }
        }

        // Keys outside any section must come first, otherwise they would end up in the previous section.
        $blocks = $globals === [] ? [] : [self::renderEntries($globals)];

        foreach ($sections as $name => $entries) {
            $blocks[] = '['.$name.']'.PHP_EOL.self::renderEntries($entries);
        }

        return implode(PHP_EOL, $blocks);
    }

    /**
     * @param array<array-key, mixed> $entries
     */
    private static function renderEntries(array $entries): string
    {
        $content = '';

        foreach ($entries as $key => $value) {
            if (!is_array($value)) {
                $content .= $key.'='.self::encode($value).PHP_EOL;
                continue;
            }

            foreach ($value as $subKey => $subValue) {
                $content .= $key.'['.$subKey.']='.self::encode($subValue).PHP_EOL;
            }
        }

        return $content;
    }

    private static function encode(mixed $value): string
    {
        return match (true) {
            $value === true => 'true',
            $value === false => 'false',
            $value === null => 'null',
            default => (string) $value,
        };
    }

    /**
     * Removes from $array1 the keys found in $array2, recursively.
     *
     * @param array<array-key, mixed> $array1
     * @param array<array-key, mixed> $array2
     *
     * @return array<array-key, mixed>
     */
    private static function arrayDiffRecursive(array $array1, array $array2): array
    {
        $result = [];

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $diff = self::arrayDiffRecursive($value, $array2[$key]);

                if ($diff !== []) {
                    $result[$key] = $diff;
                }
            }
        }

        return $result;
    }
}
