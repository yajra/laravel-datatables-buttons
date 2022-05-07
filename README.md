# Laravel DataTables Buttons Plugin

[![Laravel 9.x](https://img.shields.io/badge/Laravel-9.x-orange.svg)](http://laravel.com)
[![Latest Stable Version](https://img.shields.io/packagist/v/yajra/laravel-datatables-buttons.svg)](https://packagist.org/packages/yajra/laravel-datatables-buttons)
[![Build Status](https://travis-ci.org/yajra/laravel-datatables-buttons.svg?branch=master)](https://travis-ci.org/yajra/laravel-datatables-buttons)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yajra/laravel-datatables-buttons/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yajra/laravel-datatables-buttons/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/yajra/laravel-datatables-buttons.svg)](https://packagist.org/packages/yajra/laravel-datatables-buttons)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/yajra/laravel-datatables-buttons)

This package is a plugin of [Laravel DataTables](https://github.com/yajra/laravel-datatables) for handling server-side function of exporting the table as csv, excel, pdf and printing.

## Requirements

- [PHP >= 8.0.2](http://php.net/)
- [Laravel 9.x](https://github.com/laravel/framework)
- [Laravel DataTables](https://github.com/yajra/laravel-datatables)
- [jQuery DataTables v1.10.x](http://datatables.net/)
- [jQuery DataTables Buttons Extension](https://datatables.net/reference/button/)

## Documentations

- [Laravel DataTables Documentation](http://yajrabox.com/docs/laravel-datatables)

## Laravel Version Compatibility

| Laravel       | Package |
|:--------------|:--------|
| 8.x and below | 4.x     |
| 9.x           | 9.x     |

## Quick Installation

`composer require yajra/laravel-datatables-buttons:^9.0`

#### Service Provider (Optional on Laravel 5.5)

`Yajra\DataTables\ButtonsServiceProvider::class`

#### Configuration and Assets (Optional)

`$ php artisan vendor:publish --tag=datatables-buttons --force`

And that's it! Start building out some awesome DataTables!

## Contributing

Please see [CONTRIBUTING](https://github.com/yajra/laravel-datatables-buttons/blob/master/.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [aqangeles@gmail.com](mailto:aqangeles@gmail.com) instead of using the issue tracker.

## Credits

- [Arjay Angeles](https://github.com/yajra)
- [All Contributors](https://github.com/yajra/laravel-datatables-buttons/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/yajra/laravel-datatables-buttons/blob/master/LICENSE.md) for more information.
