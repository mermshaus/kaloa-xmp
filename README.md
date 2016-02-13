# Kaloa Library for PHP -- Xmp

The package as a whole is published under the MIT License. See LICENSE for full
license info.

http://www.kaloa.org/


## Quality assurance

Run all tools from this file's directory. The tools need to be available on your
system.

Unit tests:

~~~ bash
$ phpunit
~~~

Code analysis:

~~~ bash
$ phpmd ./src text codesize,design,naming
~~~


## Package-specfic notes

exiv2 <http://www.exiv2.org/> is a useful tool to work with image files. Here
are some common tasks:

List all XMP data from a file:

~~~ bash
$ exiv2 -px <file>
~~~

Extract XMP data from a file <image>.<ext> to <image>.xmp:

~~~ bash
$ exiv2 -eX <image>.<ext>
~~~
