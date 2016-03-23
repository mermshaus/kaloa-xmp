# kaloa/xmp

[![Latest Version](https://img.shields.io/github/release/mermshaus/kaloa-xmp.svg?style=flat-square)](https://github.com/mermshaus/kaloa-xmp/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/mermshaus/kaloa-xmp/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/mermshaus/kaloa-xmp/master.svg?style=flat-square)](https://travis-ci.org/mermshaus/kaloa-xmp)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/mermshaus/kaloa-xmp.svg?style=flat-square)](https://scrutinizer-ci.com/g/mermshaus/kaloa-xmp/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/mermshaus/kaloa-xmp.svg?style=flat-square)](https://scrutinizer-ci.com/g/mermshaus/kaloa-xmp)
[![Total Downloads](https://img.shields.io/packagist/dt/mermshaus/kaloa-xmp.svg?style=flat-square)](https://packagist.org/packages/kaloa/xmp)


## Install

Via Composer:

~~~ bash
$ composer require kaloa/xmp
~~~


## Requirements

The following PHP versions are supported:

- PHP 5.3
- PHP 5.4
- PHP 5.5
- PHP 5.6
- PHP 7
- HHVM


## Documentation

[exiv2](http://www.exiv2.org/) is a useful tool to work with image files. Here are some common tasks:

List all XMP data from a file:

~~~ bash
$ exiv2 -px <file>
~~~

Extract XMP data from a file `<image>.<ext>` to `<image>.xmp`:

~~~ bash
$ exiv2 -eX <image>.<ext>
~~~


## Testing

~~~ bash
$ ./vendor/bin/phpunit
~~~

Further quality assurance:

~~~ bash
$ ./vendor/bin/phpcs --standard=PSR2 ./src
$ ./vendor/bin/phpmd ./src text codesize,design,naming
~~~


## Credits

- [Marc Ermshaus](https://github.com/mermshaus)


## License

The package is published under the MIT License. See [LICENSE](https://github.com/mermshaus/kaloa-xmp/blob/master/LICENSE) for full license info.
