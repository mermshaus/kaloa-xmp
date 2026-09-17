# kaloa/xmp

## Install

Via Composer:

~~~ bash
$ composer require kaloa/xmp
~~~

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

## Credits

- [Marc Ermshaus](https://www.ermshaus.org/)

## License

The package is published under the MIT License. See [LICENSE](https://github.com/mermshaus/kaloa-xmp/blob/master/LICENSE) for full license info.
