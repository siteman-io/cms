# Siteman - the Website Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/siteman/cms.svg?style=flat-square)](https://packagist.org/packages/siteman/cms)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/siteman-io/cms/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/siteman-io/cms/actions?query=workflow%3Arun-tests+branch%3Amain)
[![codecov](https://codecov.io/gh/siteman-io/cms/graph/badge.svg?token=JMXLWUKK1R)](https://codecov.io/gh/siteman-io/cms)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/siteman-io/cms/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/siteman-io/cms/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/siteman/cms.svg?style=flat-square)](https://packagist.org/packages/siteman/cms)

![Siteman Logo](art/siteman_logo.png)

Siteman leverages the power and flexibility of Laravel and Filament to provide a straightforward content management
solution. It serves as a robust foundation for building custom applications, offering a seamless and efficient
development experience.

## Documentation
The docs can be found at [siteman.io](https://siteman.io).

## Installation

You can install the package via composer:

```bash
composer require siteman/cms
```

You can install Siteman via its own artisan command. It is interactive and will create the first user for you.

```bash
php artisan siteman:install
```

Normally the `siteman:install` command should take care of everything. If you want to install it manually, you can so
by enabling the Siteman plugin in your Filament panel.

```php

## Usage

```php
//...
$panel->plugin(SitemanPlugin::make());
//...
```

## Development

We use orchestra/testbench for a proper development experience. To get started you should fork the repository and clone
it. Next you can set up the development environment by executing the following commands:

```bash
composer install
composer prepare
composer serve
```

## Testing

We use pest as our testing framework.

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [bambamboole](https://github.com/bambamboole)
- [All Contributors](../../contributors)
- [datlechin/filament-menu-builder](https://github.com/datlechin/filament-menu-builder) I learned so much about Filament
  by reimplementing this package.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
