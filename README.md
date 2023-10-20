# This is my package filament-excel-import

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eightynine/filament-excel-import.svg?style=flat-square)](https://packagist.org/packages/eightynine/filament-excel-import)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eightynine/filament-excel-import/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/eightynine/filament-excel-import/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/eightynine/filament-excel-import/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/eightynine/filament-excel-import/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eightynine/filament-excel-import.svg?style=flat-square)](https://packagist.org/packages/eightynine/filament-excel-import)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require eightynine/filament-excel-import
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-excel-import-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-excel-import-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-excel-import-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$excelImportAction = new EightyNine\ExcelImportAction();
echo $excelImportAction->echoPhrase('Hello, EightyNine!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Eighty Nine](https://github.com/eighty9nine)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.