# Changelog

## 3.0.0 - Unreleased

### Added

- `$processSections` parameter on `ReadiniFile::get()` and the `WriteiniFile` constructor, to read and write ini files without sections ([#11](https://github.com/al3xLvs/WriteiniFile/issues/11)).
- `WriteiniFile\IniFileException`, thrown for every error.

### Changed

- PHP 8.2 or later is required.
- Strict types, typed parameters, properties and return types.
- `ReadiniFile` is `final` and no longer keeps static state. The properties of `WriteiniFile` are private.
- A value nested deeper than one level of arrays throws an `IniFileException` instead of writing `Array`.
- CI moved from Travis CI to GitHub Actions, with PHPStan and PHP-CS-Fixer. StyleCI and Coveralls are removed.

### Fixed

- Keys outside any section were written as empty sections and their values were lost.
- `rm()` with a non-array value for a section raised a `TypeError`. It now removes the whole section.
- Writing an empty file triggered a deprecation on PHP 8.1+.
- Tests no longer change the permissions of a tracked fixture and work when run as root.
