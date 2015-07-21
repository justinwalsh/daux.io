Plates
======

[![Author](http://img.shields.io/badge/author-@reinink-blue.svg?style=flat-square)](https://twitter.com/reinink)
[![Source Code](http://img.shields.io/badge/source-league/plates-blue.svg?style=flat-square)](https://github.com/thephpleague/plates)
[![Latest Version](https://img.shields.io/github/release/thephpleague/plates.svg?style=flat-square)](https://github.com/thephpleague/plates/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/thephpleague/plates/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/plates)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/plates.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/plates/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/plates.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/plates)
[![Total Downloads](https://img.shields.io/packagist/dt/league/plates.svg?style=flat-square)](https://packagist.org/packages/league/plates)

Plates is a native PHP template system that's fast, easy to use and easy to extend. It's inspired by the excellent [Twig](http://twig.sensiolabs.org/) template engine and strives to bring modern template language functionality to native PHP templates. Plates is designed for developers who prefer to use native PHP templates over compiled template languages, such as Twig or Smarty.

### Highlights

- Native PHP templates, no new [syntax](http://platesphp.com/templates/syntax/) to learn
- Plates is a template system, not a template language
- Plates encourages the use of existing PHP functions
- Increase code reuse with template [layouts](http://platesphp.com/templates/layouts/) and [inheritance](http://platesphp.com/templates/inheritance/)
- Template [folders](http://platesphp.com/engine/folders/) for grouping templates into namespaces
- [Data](http://platesphp.com/templates/data/#preassigned-and-shared-data) sharing across templates
- Preassign [data](http://platesphp.com/templates/data/#preassigned-and-shared-data) to specific templates
- Built-in [escaping](http://platesphp.com/templates/escaping/) helpers
- Easy to extend using [functions](http://platesphp.com/engine/functions/) and [extensions](http://platesphp.com/engine/extensions/)
- Framework-agnostic, will work with any project
- Decoupled design makes templates easy to test
- Composer ready and PSR-2 compliant

## Installation

Plates is available via Composer:

```json
{
    "require": {
        "league/plates": "3.*"
    }
}
```

## Documentation

Full documentation can be found at [platesphp.com](http://platesphp.com/).

## Testing

```bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/thephpleague/plates/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Jonathan Reinink](https://github.com/reinink)
- [All Contributors](https://github.com/thephpleague/plates/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/thephpleague/plates/blob/master/LICENSE) for more information.