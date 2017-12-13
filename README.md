# Snapshot testing with PHPUnit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/phpunit-snapshot-assertions.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-snapshot-assertions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/phpunit-snapshot-assertions/master.svg?style=flat-square)](https://travis-ci.org/spatie/phpunit-snapshot-assertions)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/phpunit-snapshot-assertions.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/phpunit-snapshot-assertions)
[![StyleCI](https://styleci.io/repos/75926188/shield?branch=master)](https://styleci.io/repos/75926188)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/phpunit-snapshot-assertions.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-snapshot-assertions)

> Snapshot testing is a way to test without writing actual test cases

```php
use Spatie\Snapshots\MatchesSnapshots;

class OrderTest
{
    use MatchesSnapshots;

    class test_it_casts_to_json()
    {
        $order = new Order(1);

        $this->assertMatchesJsonSnapshot($order->toJson());
    }
}
```

On the first run, the test runner will create a new snapshot.

```
> ./vendor/bin/phpunit

There was 1 incomplete test:

1) OrderTest::test_it_casts_to_json
Snapshot created for OrderTest__test_it_casts_to_json__1

OK, but incomplete, skipped, or risky tests!
Tests: 1, Assertions: 0, Incomplete: 1.
```

On subsequent runs, the test will pass as long as the snapshot doesn't change.

```
> ./vendor/bin/phpunit

OK (1 test, 1 assertion)
```

If there's a regression, the test will fail!

```php
$orderId = new Order(2); // Regression! Was `1`
```
```
> ./vendor/bin/phpunit

1) OrderTest::test_it_casts_to_json
Failed asserting that two strings are equal.
--- Expected
+++ Actual
@@ @@
Failed asserting that '{"id":2}' matches JSON string "{
    "id": 1
}

FAILURES!
Tests: 1, Assertions: 1, Failures: 1.
```

## Installation

You can install the package via composer:

```bash
composer require spatie/phpunit-snapshot-assertions
```

## Usage

To make snapshot assertions, use the `Spatie\Snapshots\MatchesSnapshots` trait in your test case class. This adds five assertion methods to the class:

- `assertMatchesSnapshot($actual)`
- `assertMatchesJsonSnapshot($actual)`
- `assertMatchesXmlSnapshot($actual)`
- `assertMatchesFileSnapshot($filePath)`
- `assertMatchesFileHashSnapshot($filePath)`

### Snapshot Testing 101

Let's do a snapshot assertion for a simple string, "foo".

```php
public function test_it_is_foo() {
    $this->assertMatchesSnapshot('foo');
}
```

The first time the assertion runs, it doesn't have a snapshot to compare the string with. The test runner generates a new snapshot and marks the test as incomplete.

```
> ./vendor/bin/phpunit

There was 1 incomplete test:

1) ExampleTest::test_it_matches_a_string
Snapshot created for ExampleTest__test_it_matches_a_string__1

OK, but incomplete, skipped, or risky tests!
Tests: 1, Assertions: 0, Incomplete: 1.
```

Snapshot ids are generated based on the test and testcase's names. Basic snapshots return a `var_export` of the actual value.

```php
<?php return 'foo';
```

Let's rerun the test. The test runner will see that there's already a snapshot for the assertion and do a comparison.

```
> ./vendor/bin/phpunit

OK (1 test, 1 assertion)
```

If we change actual value to "bar", the test will fail because the snapshot still returns "foo".

```php
public function test_it_is_foo() {
    $this->assertMatchesSnapshot('bar');
}
```
```
> ./vendor/bin/phpunit

1) ExampleTest::test_it_matches_a_string
Failed asserting that two strings are equal.
--- Expected
+++ Actual
@@ @@
-'foo'
+'bar'

FAILURES!
Tests: 1, Assertions: 1, Failures: 1.
```

When we expect a changed value, we need to tell the test runner to update the existing snapshots instead of failing the test. This is possible by adding  a`-d --update-snapshots` flag to the `phpunit` command.

```
> ./vendor/bin/phpunit -d --update-snapshots

OK (1 test, 1 assertion)
```

As a result, our snapshot file returns "bar" instead of "foo".

```php
<?php return 'bar';
```

### File snapshots

The `MatchesSnapshots` trait offers two ways to assert that a file is identical to the snapshot that was made the first time the test was run:

The `assertMatchesFileHashSnapshot($filePath)` assertion asserts that the hash of the file passed into the function and the hash saved in the snapshot match. This assertion is fast and uses very little disk space. The downside of this assertion is that there is no easy way to see how the two files differ if the test fails. 
    
The `assertMatchesFileSnapshot($filePath)` assertion works almost the same way as the file hash assertion, except that it actually saves the whole file in the snapshots directory. If the assertion fails, it places the failed file next to the snapshot file so they can easily be manually compared. The persisted failed file is automatically deleted when the test passes. This assertion is most useful when working with binary files that should be manually compared like images or pdfs.

### Customizing Snapshot Ids and Directories

Snapshot ids are generated via the `getSnapshotId` method on the `MatchesSnapshot` trait. Override the method to customize the id. By default, a snapshot id exists of the test name, the test case name and an incrementing value, e.g. `Test__my_test_case__1`.

#### Example: Replacing the `__` Delimiter With `--`

```php
protected function getSnapshotId(): string
{
    return (new ReflectionClass($this))->getShortName().'--'.
        $this->getName().'--'.
        $this->snapshotIncrementor;
}
```

By default, snapshots are stored in a `__snapshots__` directory relative to the test class. This can be changed by overriding the `getSnapshotDirectory` method.

#### Example: Renaming the `__snapshots__` directory to `snapshots`

```php
protected function getSnapshotDirectory(): string
{
    return dirname((new ReflectionClass($this))->getFileName()).
        DIRECTORY_SEPARATOR.
        'snapshots';
}
```

### Writing Custom Drivers

Drivers ensure that different types of data can be serialized and matched in their own way. A driver is a class that implements the `Spatie\Snapshots\Driver` interface, which requires three method implementations: `serialize`, `extension` and `match`.

Let's take a quick quick look at the `JsonDriver`.

```php
namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class JsonDriver implements Driver
{
    public function serialize($data): string
    {
        if (! is_string($data)) {
            throw new CantBeSerialized('Only strings can be serialized to json');
        }

        return json_encode(json_decode($data), JSON_PRETTY_PRINT).PHP_EOL;
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        Assert::assertJsonStringEqualsJsonString($actual, $expected);
    }
}
```

- The `serialize` method returns a string which will be written to the snapshot file. In the `JsonDriver`, we'll decode and re-encode the json string to ensure the snapshot has pretty printing.
- We want to save json snapshots as json files, so we'll use `json` as their file extension.
- When matching the expected data with the actual data, we want to use PHPUnit's built in json assertions, so we'll call the specific `assertJsonStringEqualsJsonString` method.

Drivers can be used by passing them as `assertMatchesSnapshot`'s second argument.

```php
$this->assertMatchesSnapshot($something->toYaml(), new MyYamlDriver());
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [Alex Vanderbist](https://github.com/alexvanderbist)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
