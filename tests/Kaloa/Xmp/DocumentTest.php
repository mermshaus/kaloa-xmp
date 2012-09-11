<?php
/**
 * Kaloa Library (http://www.kaloa.org/)
 *
 * @license http://www.kaloa.org/license.txt MIT License
 */

namespace Kaloa\Tests;

use DateTime;
use PHPUnit_Framework_TestCase;

use Kaloa\Xmp\Document as XmpDocument;
use Kaloa\Xmp\Reader as XmpReader;

use Kaloa\Xmp\Properties\DublinCoreProperties;
use Kaloa\Xmp\Properties\ExifProperties;

/**
 *
 */
class DocumentTest extends PHPUnit_Framework_TestCase
{
    protected function formatOutputDc(DublinCoreProperties $prop)
    {
        $lines = array();

        $f = function ($what, $isArray = true) use ($prop, &$lines) {
            $methodName = 'get' . $what;

            if ($isArray) {
                foreach ($prop->$methodName() as $tmp) {
                    if ($tmp !== null && $tmp !== '') {
                        $lines[] = 'Xmp.dc.' . $what . ': ' . $tmp;
                    }
                }
            } else {
                $tmp = $prop->$methodName();
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

    protected function formatOutputExif(ExifProperties $prop)
    {
        $lines = array();

        $f = function ($what, $isArray = true) use ($prop, &$lines) {
            $methodName = 'get' . $what;

            if ($isArray) {
                foreach ($prop->$methodName() as $tmp) {
                    if ($tmp !== null && $tmp !== '') {
                        $lines[] = 'Xmp.exif.' . $what . ': ' . $tmp;
                    }
                }
            } else {
                $tmp = $prop->$methodName();

                if ($tmp !== null && $tmp !== '') {
                    if ($tmp instanceof DateTime) {
                        $tmp = $tmp->format('Y-m-d\TH:i:s.uP');
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

    public function testXmpDataCanBeProcessed()
    {
        $provider = array(
            'example005',
            'louisiana-ng',
            'namespaces'
        );

        $xmpReader = new XmpReader();

        foreach ($provider as $name) {
            $stream = fopen(__DIR__ . '/data/' . $name . '.xmp', 'rb');
            $xmpDocument = $xmpReader->getXmpDocument($stream);
            fclose($stream);

            $test = $this->formatOutputDc($xmpDocument->getDublinCoreProperties())
                    . "\n"
                    . $this->formatOutputExif($xmpDocument->getExifProperties());

            $this->assertEquals(file_get_contents(__DIR__ . '/data/' . $name . '.expected'), $test);
        }
    }

    /**
     * @expectedException Kaloa\Xmp\ReaderException
     */
    public function testErroneousXmpDataThrowsException()
    {
        $xmpReader = new XmpReader();
        $stream = fopen(__DIR__ . '/data/err-incomplete.xmp', 'rb');
        $xmpReader->getXmpDocument($stream);
        fclose($stream);
    }

    /**
     * @expectedException Kaloa\Xmp\ReaderException
     */
    public function testMissingXmpDataThrowsException()
    {
        $xmpReader = new XmpReader();
        $stream = fopen(__DIR__ . '/data/err-notfound.xmp', 'rb');
        $xmpReader->getXmpDocument($stream);
        fclose($stream);
    }

    /**
     * @expectedException Kaloa\Xmp\ReaderException
     */
    public function testInvalidStreamThrowsException()
    {
        $xmpReader = new XmpReader();
        $xmpReader->getXmpDocument(false);
    }
}
