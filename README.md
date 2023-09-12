# Short Description Of CoreService

Core service library for Laravel project

## Installation

You can install the package via composer:

```bash
composer require themelooks/coreservice
```

## Usage
Add this lines under providers array in config/app.php
``` php
ThemeLooks\CoreService\CoreServiceProvider::class,
ThemeLooks\CoreService\CoreRouteServiceProvider::class,
ThemeLooks\CoreService\PluginServiceProvider::class,
ThemeLooks\CoreService\PluginRouteServices::class,
ThemeLooks\CoreService\ThemeServiceProvider::class,
```
### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


### Security

If you discover any security related issues, please email kousar.themelooks@gmail.com instead of using the issue tracker.

## Credits

- [Kousar Rahman](https://github.com/kousar2334)
- [Themelooks](http://themelooks.com/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
