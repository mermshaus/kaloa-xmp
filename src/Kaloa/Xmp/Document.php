<?php
/**
 * Kaloa Library (http://www.kaloa.org/)
 *
 * @license http://www.kaloa.org/license.txt MIT License
 */

namespace Kaloa\Xmp;

use DOMDocument;
use DOMXPath;

use Kaloa\Xmp\Properties\DublinCoreProperties;
use Kaloa\Xmp\Properties\ExifProperties;

/**
 *
 */
class Document
{
    /**
     * Stores original XMP XML data
     *
     * @var DOMDocument $dom
     */
    private $dom;

    /**
     *
     * @var DublinCoreProperties
     */
    private $dublinCoreProperties;

    /**
     *
     * @var ExifProperties
     */
    private $exifProperties;

    /**
     *
     * @param DOMDocument $dom
     */
    public function __construct(DOMDocument $dom)
    {
        $this->dom   = $dom;

        $xPath = new DOMXPath($dom);
        $xPath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');
        $xPath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xPath->registerNamespace('exif', 'http://ns.adobe.com/exif/1.0/');

        $this->dublinCoreProperties = new DublinCoreProperties($xPath);
        $this->exifProperties = new ExifProperties($xPath);
    }

    /**
     *
     * @return DublinCoreProperties
     */
    public function getDublinCoreProperties()
    {
        return $this->dublinCoreProperties;
    }

    /**
     *
     * @return ExifProperties
     */
    public function getExifProperties()
    {
        return $this->exifProperties;
    }
}
