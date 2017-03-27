# Snapshot testing with PHPUnit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/phpunit-snapshot-assertions.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-snapshot-assertions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/phpunit-snapshot-assertions/master.svg?style=flat-square)](https://travis-ci.org/spatie/phpunit-snapshot-assertions)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/phpunit-snapshot-assertions.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/phpunit-snapshot-assertions)
[![StyleCI](https://styleci.io/repos/75926188/shield?branch=master)](https://styleci.io/repos/75926188)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/phpunit-snapshot-assertions.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-snapshot-assertions)

```php
use Spatie\Snapshots\MatchesSnapshots;

class OrderSerializerTest
{
    use MatchesSnapshot;

    class test_it_serializes_an_order_json()
    {
        $serializer = new JsonOrderSerializer();

        $this->assertMatchesJsonSnapshot($serializer->serialize(new Order(1));
    }
}
```

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

The best postcards are published [on our website](https://spatie.be/en/opensource/postcards).

## Installation

You can install the package via composer:

``` bash
composer require spatie/phpunit-snapshot-assertions
```

## Usage

*Todo*

```
assertMatchesSnapshot
assertMatchesJsonSnapshot
assertMatchesXmlSnapshot
```

### Writing Custom Drivers

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

## Credits

- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [Alex Vanderbist](https://github.com/alexvanderbist)
- [All Contributors](../../contributors)

## About Spatie

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
