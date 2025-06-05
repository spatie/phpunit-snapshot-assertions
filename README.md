<div align="left">
    <a href="https://spatie.be/open-source?utm_source=github&utm_medium=banner&utm_campaign=phpunit-snapshot-assertions">
      <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://spatie.be/packages/header/phpunit-snapshot-assertions/html/dark.webp">
        <img alt="Logo for phpunit-snapshot-assertions" src="https://spatie.be/packages/header/phpunit-snapshot-assertions/html/light.webp">
      </picture>
    </a>

<h1>Snapshot testing with PHPUnit</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/phpunit-snapshot-assertions.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-snapshot-assertions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![run-tests](https://img.shields.io/github/actions/workflow/status/spatie/phpunit-snapshot-assertions/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/phpunit-snapshot-assertions.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-snapshot-assertions)
    
</div>

Snapshot testing is a way to test without writing actual test cases.

You can learn more in [this free video](https://spatie.be/courses/testing-laravel-with-pest/snapshot-testing) from our Testing Laravel course. Don't worry you can use this package in non-Laravel projects too.

```php
use Spatie\Snapshots\MatchesSnapshots;

class OrderTest
{
    use MatchesSnapshots;

    public function test_it_casts_to_json()
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

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/phpunit-snapshot-assertions.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/phpunit-snapshot-assertions)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require --dev spatie/phpunit-snapshot-assertions
```

## Usage

To make snapshot assertions, use the `Spatie\Snapshots\MatchesSnapshots` trait in your test case class. This adds a set of assertion methods to the class:

- `assertMatchesSnapshot($actual)`
- `assertMatchesFileHashSnapshot($actual)`
- `assertMatchesFileSnapshot($actual)`
- `assertMatchesHtmlSnapshot($actual)`
- `assertMatchesJsonSnapshot($actual)`
- `assertMatchesObjectSnapshot($actual)`
- `assertMatchesTextSnapshot($actual)`
- `assertMatchesXmlSnapshot($actual)`
- `assertMatchesYamlSnapshot($actual)`
- `assertMatchesImageSnapshot($actual)`

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

Snapshot ids are generated based on the test and testcase's names. Basic snapshots return a plain text or YAML representation of the actual value.

```txt
foo
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

When we expect a changed value, we need to tell the test runner to update the existing snapshots instead of failing the test. This is possible by adding  a`-d --update-snapshots` flag to the `phpunit` command, or setting the `UPDATE_SNAPSHOTS` env var to `true`.

```
> ./vendor/bin/phpunit -d --update-snapshots

OK (1 test, 1 assertion)
```

As a result, our snapshot file returns "bar" instead of "foo".

```txt
bar
```

### File snapshots

The `MatchesSnapshots` trait offers two ways to assert that a file is identical to the snapshot that was made the first time the test was run:

The `assertMatchesFileHashSnapshot($filePath)` assertion asserts that the hash of the file passed into the function and the hash saved in the snapshot match. This assertion is fast and uses very little disk space. The downside of this assertion is that there is no easy way to see how the two files differ if the test fails.

The `assertMatchesFileSnapshot($filePath)` assertion works almost the same way as the file hash assertion, except that it actually saves the whole file in the snapshots directory. If the assertion fails, it places the failed file next to the snapshot file so they can easily be manually compared. The persisted failed file is automatically deleted when the test passes. This assertion is most useful when working with binary files that should be manually compared like images or pdfs.

### Image snapshots

The `assertImageSnapshot` requires the [spatie/pixelmatch-php](https://github.com/spatie/pixelmatch-php) package to be installed.

This assertion will pass if the given image is nearly identical to the snapshot that was made the first time the test was run. You can customize the threshold by passing a second argument to the assertion. Higher values will make the comparison more sensitive. The threshold should be between 0 and 1.

```php 
$this->assertMatchesImageSnapshot($imagePath, 0.1);
```


### Customizing Snapshot Ids and Directories

Snapshot ids are generated via the `getSnapshotId` method on the `MatchesSnapshot` trait. Override the method to customize the id. By default, a snapshot id exists of the test name, the test case name and an incrementing value, e.g. `Test__my_test_case__1`.

#### Example: Replacing the `__` Delimiter With `--`

```php
protected function getSnapshotId(): string
{
    return (new ReflectionClass($this))->getShortName().'--'.
        $this->name().'--'.
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

### Using specific Drivers

The driver used to serialize the data can be specificied as second argument of the
`assertMatchesSnapshot` method, so you can pick one that better suits your needs:

```php
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\MatchesSnapshots;

class OrderTest
{
    use MatchesSnapshots;

    public function test_snapshot_with_json_driver()
    {
        $order = new Order(1);

        $this->assertMatchesSnapshot($order->toJson(), new JsonDriver());
    }
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

### Usage in CI

When running your tests in Continuous Integration you would possibly want to disable the creation of snapshots.

By using the `--without-creating-snapshots` parameter or by setting the `CREATE_SNAPSHOTS` env var to `false`, PHPUnit will fail if the snapshots don't exist.

```bash
> ./vendor/bin/phpunit -d --without-creating-snapshots

1) ExampleTest::test_it_matches_a_string
Snapshot "ExampleTest__test_it_matches_a_string__1.txt" does not exist.
You can automatically create it by removing the `CREATE_SNAPSHOTS=false` env var, or `-d --no-create-snapshots` of PHPUnit's CLI arguments.
```

### Usage with parallel testing

If you want to run your test in parallel with a tool like [Paratest](https://github.com/paratestphp/paratest), ou with the `php artisan test --parallel` command of Laravel, you will have to use the environment variables.


```bash
> CREATE_SNAPSHOTS=false php artisan test --parallel

1) ExampleTest::test_it_matches_a_string
Snapshot "ExampleTest__test_it_matches_a_string__1.txt" does not exist.
You can automatically create it by removing the `CREATE_SNAPSHOTS=false` env var, or `-d --no-create-snapshots` of PHPUnit's CLI arguments.
```

### A note for Windows users

Windows users should configure their line endings in `.gitattributes`.

```txt
# Snapshots used in tests hold serialized data and their line ending should be left unchanged
tests/**/__snapshots__/** binary
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [Alex Vanderbist](https://github.com/alexvanderbist)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
