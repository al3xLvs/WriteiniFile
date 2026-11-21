# WriteiniFile

PHP library to create, read, update, add, remove and erase ini files.

[![CI](https://github.com/al3xLvs/WriteiniFile/actions/workflows/ci.yml/badge.svg)](https://github.com/al3xLvs/WriteiniFile/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/magicalex/write-ini-file)](https://packagist.org/packages/magicalex/write-ini-file)
[![PHP Version](https://img.shields.io/packagist/dependency-v/magicalex/write-ini-file/php)](https://packagist.org/packages/magicalex/write-ini-file)
[![Total Downloads](https://img.shields.io/packagist/dt/magicalex/write-ini-file)](https://packagist.org/packages/magicalex/write-ini-file)
[![License](https://img.shields.io/packagist/l/magicalex/write-ini-file)](LICENSE)

## Installation

Requires PHP 8.2 or later.

```bash
composer require magicalex/write-ini-file
```

## Usage

```php
<?php

require_once 'vendor/autoload.php';

use WriteiniFile\ReadiniFile;
use WriteiniFile\WriteiniFile;

$data = [
    'fruit' => ['orange' => '100g', 'fraise' => '10g'],
    'legume' => ['haricot' => '20g', 'oignon' => '100g'],
    'jus' => ['orange' => '1L', 'pomme' => '1,5L', 'pamplemousse' => '0,5L'],
];

(new WriteiniFile('file.ini'))
    ->create($data)
    ->add(['music' => ['rap' => true, 'rock' => false]])
    ->rm(['jus' => ['pomme' => '1,5L']])
    ->update(['fruit' => ['orange' => '200g']])
    ->write();
```

`file.ini` now contains:

```ini
[fruit]
orange=200g
fraise=10g

[legume]
haricot=20g
oignon=100g

[jus]
orange=1L
pamplemousse=0,5L

[music]
rap=true
rock=false
```

Read it back:

```php
var_dump(ReadiniFile::get('file.ini'));
/*
array(4) {
  ["fruit"]=> array(2) { ["orange"]=> string(4) "200g" ["fraise"]=> string(3) "10g" }
  ["legume"]=> array(2) { ["haricot"]=> string(3) "20g" ["oignon"]=> string(4) "100g" }
  ["jus"]=> array(2) { ["orange"]=> string(2) "1L" ["pamplemousse"]=> string(4) "0,5L" }
  ["music"]=> array(2) { ["rap"]=> string(4) "true" ["rock"]=> string(5) "false" }
}
*/
```

Empty it:

```php
(new WriteiniFile('file.ini'))->erase()->write();
```

### Methods

| Method | Description |
| --- | --- |
| `create(array $data)` | Replaces the whole content. |
| `update(array $data)` | Changes existing values and adds the missing ones. |
| `add(array $data)` | Adds values. A key that already exists keeps its value and gets the new one too, as an array. |
| `rm(array $data)` | Removes the given keys, whatever their values. `rm(['jus' => true])` removes the whole section. |
| `erase()` | Removes all the content. |
| `write()` | Writes the file. Nothing is saved until you call it. |

### Ini files without sections

Pass `processSections: false` to read or write a file whose keys are not grouped in sections:

```ini
name=WriteiniFile
debug=false
```

```php
(new WriteiniFile('config.ini', processSections: false))
    ->update(['debug' => true])
    ->write();

ReadiniFile::get('config.ini', processSections: false);
// ['name' => 'WriteiniFile', 'debug' => 'true']
```

With sections (the default), keys that are not in a section are written at the top of the file.

### Scanner mode

Values are read as raw strings (`INI_SCANNER_RAW`) by default. Pass `INI_SCANNER_TYPED` to get booleans, integers and `null`, or `INI_SCANNER_NORMAL` for PHP's default behavior. See [`parse_ini_file()`](https://www.php.net/parse_ini_file).

```php
ReadiniFile::get('file.ini', INI_SCANNER_TYPED);
new WriteiniFile('file.ini', INI_SCANNER_TYPED);
```

### Errors

Every error throws a `WriteiniFile\IniFileException`, which extends `RuntimeException`:

- `ReadiniFile::get()` on a missing file;
- a file that cannot be parsed;
- a file that cannot be written;
- a value nested deeper than one level of arrays, which the ini format cannot represent.

## Upgrading from 2.x

- PHP 8.2 or later is required.
- Errors throw `WriteiniFile\IniFileException` instead of `Exception`. It extends `Exception`, so existing `catch (\Exception $e)` blocks still work, and the messages are unchanged.
- Parameters are typed: the path must be a string and the scanner mode an integer.
- `ReadiniFile` is `final`, and the properties of `WriteiniFile` are private. Fluent methods declare a `static` return type and `write()` declares `bool`, so subclasses that override them must declare the same.
- Keys outside any section are written at the top of the file. Before, each one was written as an empty section and its value was lost.
- `rm()` with a non-array value for a section removes the whole section. Before, it raised a `TypeError`.
- A value nested too deeply throws an `IniFileException`. Before, `Array` was written to the file.

## Contributing

```bash
composer install
composer test      # PHPUnit
composer analyse   # PHPStan
composer cs        # check the code style, or `composer cs:fix` to fix it
```

## License

WriteiniFile is released under the [GNU General Public License v3.0](LICENSE).
