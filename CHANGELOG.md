# Changelog

All notable changes to `phpunit-snapshot-assertions` will be documented in this file

## 4.2.15 - 2022-08-25

### What's Changed

- Apply PCRE_UTF8 for cleaning filename by @su-kun1899 in https://github.com/spatie/phpunit-snapshot-assertions/pull/147

### New Contributors

- @su-kun1899 made their first contribution in https://github.com/spatie/phpunit-snapshot-assertions/pull/147

**Full Changelog**: https://github.com/spatie/phpunit-snapshot-assertions/compare/4.2.14...4.2.15

## 4.2.14 - 2022-07-29

### What's Changed

- Add tests for Text Driver and Windows EOL fixes by @mallardduck in https://github.com/spatie/phpunit-snapshot-assertions/pull/146

**Full Changelog**: https://github.com/spatie/phpunit-snapshot-assertions/compare/4.2.13...4.2.14

## 4.2.13 - 2022-06-26

### What's Changed

- Add test to cover HTML without doctype by @mallardduck in https://github.com/spatie/phpunit-snapshot-assertions/pull/144
- Add type safe json matching by @cschindl in https://github.com/spatie/phpunit-snapshot-assertions/pull/145

### New Contributors

- @cschindl made their first contribution in https://github.com/spatie/phpunit-snapshot-assertions/pull/145

**Full Changelog**: https://github.com/spatie/phpunit-snapshot-assertions/compare/4.2.12...4.2.13

## 4.2.12 - 2022-05-31

### What's Changed

- Use `LIBXML_HTML_NODEFDTD` to maintain output consistency by @JayBizzle in https://github.com/spatie/phpunit-snapshot-assertions/pull/141

### New Contributors

- @JayBizzle made their first contribution in https://github.com/spatie/phpunit-snapshot-assertions/pull/141

**Full Changelog**: https://github.com/spatie/phpunit-snapshot-assertions/compare/4.2.11...4.2.12

## 4.2.11 - 2022-03-18

## What's Changed

- revert match process in JsonDriver by @Stevemoretz in https://github.com/spatie/phpunit-snapshot-assertions/pull/138

## New Contributors

- @Stevemoretz made their first contribution in https://github.com/spatie/phpunit-snapshot-assertions/pull/138

**Full Changelog**: https://github.com/spatie/phpunit-snapshot-assertions/compare/4.2.10...4.2.11

## 4.2.10 - 2022-02-08

## What's Changed

- Normalize windows line endings on HTML tests by @mallardduck in https://github.com/spatie/phpunit-snapshot-assertions/pull/136

## New Contributors

- @mallardduck made their first contribution in https://github.com/spatie/phpunit-snapshot-assertions/pull/136

**Full Changelog**: https://github.com/spatie/phpunit-snapshot-assertions/compare/4.2.9...4.2.10

## 4.2.9 - 2021-12-03

## What's Changed

- Do not convert stdClass to array && remove outdated piece of code by @alshenetsky in https://github.com/spatie/phpunit-snapshot-assertions/pull/134

## New Contributors

- @alshenetsky made their first contribution in https://github.com/spatie/phpunit-snapshot-assertions/pull/134

**Full Changelog**: https://github.com/spatie/phpunit-snapshot-assertions/compare/4.2.8...4.2.9

## 4.2.8 - 2021-12-02

## What's Changed

- Update php-cs-fixer config by @gndk in https://github.com/spatie/phpunit-snapshot-assertions/pull/132
- Allow Symfony 6.0 by @gndk in https://github.com/spatie/phpunit-snapshot-assertions/pull/133

## New Contributors

- @gndk made their first contribution in https://github.com/spatie/phpunit-snapshot-assertions/pull/132

**Full Changelog**: https://github.com/spatie/phpunit-snapshot-assertions/compare/4.2.7...4.2.8

## 4.2.7 - 2021-07-09

- clean filenames on MatchesFileSnapshot for Windows (#130)

## 4.2.6 - 2021-04-20

- allow using env vars to manage snapshot creation and update (#126)

## 4.2.5 - 2021-01-27

- add support for PHP 7.3

## 4.2.4 - 2020-11-26

- add support for PHP 8

## 4.2.3 - 2020-11-03

- migrate phpunit config

## 4.2.2 - 2020-06-01

- moves default implementation of snapshot directory/id to concerns directory (#99)

## 4.2.1 - 2020-05-18

- Fix exception message

## 4.2.0 - 2020-05-11

- Fixed inconsistent line endings
- Improved Windows support

## 4.1.0 - 2020-04-08

- Added `--without-creating-snapshots` flag

## 4.0.0 - 2020-02-11

*Snapshots generated with `assertMatchesSnapshot` will break when upgrading to this version. The easiest way to upgrade is to ensure your snapshot tests pass, delete your snapshots, upgrade the package, then rerun the tests to generate new snapshots.*

- New snapshot formats when using `assertMatchesSnapshot`: scalars (strings, integers & floats) are serialized to `txt` files, objects & arrays are serialized to `yaml` files
- New `TextDriver` to store snapshots in `txt` files
- New `ObjectDriver` to serialize data to YAML and store snapshots in `yaml` files
- Removed `VarDriver`

## 3.1.1 - 2019-02-10

- Add support for PHPUnit 9 (#86)

## 3.1.0 - 2019-12-02

- Drop support for PHP 7.3

## 3.0.0 - 2019-11-22

- `assertMatchesJsonSnapshot` now supports all JSON serializable objects, and won't convert empty arrays to obejcts anymore

## 2.2.1 - 2019-11-22

- Allow symfony 5 components

## 2.2.0 - 2019-10-23

- Added an `assertMatchesHtmlSnapshot` assertion

## 2.1.3 - 2019-08-07

- Allow recursive mkdir for file snapshots

## 2.1.2 - 2019-03-27

- Less restrictive symfony/yaml version requirement

## 2.1.1 - 2019-03-04

- Fix for json driver

## 2.1.0 - 2019-02-07

- Require PHPUnit 8 & PHP 7.2

## 2.0.0 - 2019-01-29

- Use YAML by default for associative arrays
- Drop PHP 7.0 support, the new constraint is ^7.1
- Drop PHPUnit ^6.5 support, the new constraint is ^7.0

## 1.4.1 - 2019-01-29

- Fix JSON array comparisons

## 1.4.0 - 2019-01-29

- Allow arrays to be serialized to JSON

## 1.3.3 - 2018-12-15

- Fix updating failed file-snapshots

## 1.3.2 - 2018-10-18

- Fix for tests with weird characters

## 1.3.1 - 2018-06-09

- Lowered minimum required PHPUnit version

## 1.3.0 - 2018-05-22

- Only mark test incomplete after every snapshot has been run

## 1.2.3 - 2018-03-15

- Fixed snapshot creation in recursive directories

## 1.2.2 - 2018-02-17

- Support PHP 7.2

## 1.2.1 - 2018-02-02

- Support phpunit ^7.0

## 1.2.0 - 2017-11-29

- Added `assertMatchesFileSnapshot`

## 1.1.1 - 2017-10-11

- Fixed `assertMatchesFileHashSnapshot`

## 1.1.0 - 2017-10-07

- Added `assertMatchesFileHashSnapshot` assertion

## 1.0.2 - 2017-09-11

- Added `example` folder to `.gitattributes`

## 1.0.1 - 2017-06-23

- Fixed expected and actual argument order when making json assertions

## 1.0.0 - 2017-05-29

- Added a reminder how to update snapshots when a snapshot assertion fails

## 0.4.1 - 2017-03-27

- Initial release
