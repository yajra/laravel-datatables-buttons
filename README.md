# Laravel DataTables Buttons Plugin

[![Laravel 5.4|5.5](https://img.shields.io/badge/Laravel-5.4|5.5-orange.svg)](http://laravel.com)
[![Latest Stable Version](https://img.shields.io/packagist/v/yajra/laravel-datatables-buttons.svg)](https://packagist.org/packages/yajra/laravel-datatables-buttons)
[![Build Status](https://travis-ci.org/yajra/laravel-datatables-buttons.svg?branch=master)](https://travis-ci.org/yajra/laravel-datatables-buttons)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yajra/laravel-datatables-buttons/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yajra/laravel-datatables-buttons/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/yajra/laravel-datatables-buttons.svg)](https://packagist.org/packages/yajra/laravel-datatables-buttons)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/yajra/laravel-datatables-buttons)

This package is a plugin of [Laravel DataTables](https://github.com/yajra/laravel-datatables) for handling server-side function of exporting the table as csv, excel, pdf and printing.

## Requirements
- [PHP >=7.0](http://php.net/)
- [Laravel 5.4|5.5](https://github.com/laravel/framework)
- [jQuery DataTables v1.10.x](http://datatables.net/)
- [jQuery DataTables Buttons Extension](https://datatables.net/reference/button/)

## Documentations
- [Laravel DataTables Documentation](http://yajrabox.com/docs/laravel-datatables)

## NOTE
This version is still on experimental stage. Until Laravel DataTables v8.x stable version is released, 
there might be a breaking changes that may be included on future releases. 

## Quick Installation
`composer require yajra/laravel-datatables-buttons:^3.0`

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

## Buy me a coffee
[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/yajra)
<a href='https://www.patreon.com/bePatron?u=4521203'><img alt='Become a Patron' src='https://s3.amazonaws.com/patreon_public_assets/toolbox/patreon.png' border='0' width='200px' ></a>
