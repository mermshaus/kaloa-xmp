<?php

declare(strict_types=1);

/*
 * This file is part of the kaloa/xmp package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Xmp\Tests;

use DateTime;
use Kaloa\Xmp\Properties\DublinCoreProperties;
use Kaloa\Xmp\Properties\ExifProperties;
use Kaloa\Xmp\Reader;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    private function formatOutputDc(DublinCoreProperties $prop): string
    {
        $lines = [];

        $f = function ($what, $isArray = true) use ($prop, &$lines) {
            $methodName = 'get' . $what;

            if ($isArray) {
                foreach ($prop->{$methodName}() as $tmp) {
                    if ($tmp !== null && $tmp !== '') {
                        $lines[] = 'Xmp.dc.' . $what . ': ' . $tmp;
                    }
                }
            } else {
                $tmp = $prop->{$methodName}();
                if ($tmp !== null && $tmp !== '') {
                    $lines[] = 'Xmp.dc.' . $what . ': ' . $tmp;
                }
            }
        };

        $f('Contributor');
        $f('Coverage', false);
        $f('Creator');
        $f('Date');
        $f('Description');
        $f('Format', false);
        $f('Identifier', false);
        $f('Language');
        $f('Publisher');
        $f('Relation');
        $f('Rights');
        $f('Source', false);
        $f('Subject');
        $f('Title');
        $f('Type');

        return implode("\n", $lines) . "\n";
    }

    private function formatOutputExif(ExifProperties $prop): string
    {
        $lines = [];

        $f = function ($what, $isArray = true) use ($prop, &$lines) {
            $methodName = 'get' . $what;

            if ($isArray) {
                foreach ($prop->{$methodName}() as $tmp) {
                    if ($tmp !== null && $tmp !== '') {
                        $lines[] = 'Xmp.exif.' . $what . ': ' . $tmp;
                    }
                }
            } else {
                $tmp = $prop->{$methodName}();

                if ($tmp !== null && $tmp !== '') {
                    if ($tmp instanceof DateTime) {
                        $tmp = $tmp->format('Y-m-d\\TH:i:s.uP');
                    }

                    $lines[] = 'Xmp.exif.' . $what . ': ' . $tmp;
                }
            }
        };

        $f('DateTimeOriginal', false);
        $f('ExifVersion', false);
        $f('PixelXDimension', false);
        $f('PixelYDimension', false);

        return implode("\n", $lines) . "\n";
    }

    public function testXmpDataCanBeProcessed(): void
    {
        $provider = ['example005', 'louisiana-ng', 'namespaces'];

        $xmpReader = new Reader();

        foreach ($provider as $name) {
            $stream = fopen(__DIR__ . '/data/' . $name . '.xmp', 'rb');
            $xmpDocument = $xmpReader->getXmpDocument($stream);
            fclose($stream);

            $test =
                $this->formatOutputDc($xmpDocument->getDublinCoreProperties()) .
                "\n" .
                $this->formatOutputExif($xmpDocument->getExifProperties());

            $this->assertEquals(file_get_contents(__DIR__ . '/data/' . $name . '.expected'), $test);
        }
    }

    public function testErroneousXmpDataThrowsException(): void
    {
        $this->expectException('Kaloa\\Xmp\\ReaderException');

        $xmpReader = new Reader();
        $stream = fopen(__DIR__ . '/data/err-incomplete.xmp', 'rb');
        $xmpReader->getXmpDocument($stream);
        fclose($stream);
    }

    public function testMissingXmpDataThrowsException(): void
    {
        $this->expectException('Kaloa\\Xmp\\ReaderException');

        $xmpReader = new Reader();
        $stream = fopen(__DIR__ . '/data/err-notfound.xmp', 'rb');
        $xmpReader->getXmpDocument($stream);
        fclose($stream);
    }

    public function testInvalidStreamThrowsException(): void
    {
        $this->expectException('Kaloa\\Xmp\\ReaderException');

        $xmpReader = new Reader();
        $xmpReader->getXmpDocument(false);
    }
}
