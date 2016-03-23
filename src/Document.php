<?php

/*
 * This file is part of the kaloa/xmp package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Xmp;

use DOMDocument;
use DOMXPath;

use Kaloa\Xmp\Properties\DublinCoreProperties;
use Kaloa\Xmp\Properties\ExifProperties;

/**
 * Provides a read-only interface to an Extensible Metadata Platform (XMP)
 * document.
 *
 * XMP data can be embedded within a variety of media file formats. For
 * instance, many image editors use XMP to add metadata to image files. This
 * class was originally written to retrieve data from digital photos.
 *
 * Usage example:
 *
 * <pre>
 * use Kaloa\Xmp\Reader;
 *
 * $stream = fopen('/path/to/image.jpg', 'rb');
 * $reader = new Reader();
 * $xmpDocument = $reader->getXmpDocument($stream);
 * fclose($stream);
 * $dcProps = $xmpDocument->getDublinCoreProperties();
 *
 * printf("Image title(s): %s\n", implode(', ', $dcProps->getTitle()));
 * printf("Image tags: %s\n", implode(', ', $dcProps->getSubject()));
 * </pre>
 */
class Document
{
    /**
     * Original XMP XML data.
     *
     * @var DOMDocument $dom
     */
    private $dom;

    /**
     * DublinCore schema properties found in the XMP document.
     *
     * @var DublinCoreProperties
     */
    private $dublinCoreProperties;

    /**
     * Exif schema properties found in the XMP document.
     *
     * @var ExifProperties
     */
    private $exifProperties;

    /**
     * Initializes the instance.
     *
     * @param DOMDocument $dom XMP document
     */
    public function __construct(DOMDocument $dom)
    {
        $this->dom = $dom;

        $xPath = new DOMXPath($dom);

        $xPath->registerNamespace(
            'dc',
            'http://purl.org/dc/elements/1.1/'
        );
        $xPath->registerNamespace(
            'rdf',
            'http://www.w3.org/1999/02/22-rdf-syntax-ns#'
        );
        $xPath->registerNamespace(
            'exif',
            'http://ns.adobe.com/exif/1.0/'
        );

        $this->dublinCoreProperties = new DublinCoreProperties($xPath);
        $this->exifProperties = new ExifProperties($xPath);
    }

    /**
     * Returns Dublin Core (DC) schema properties found in the document.
     *
     * @return DublinCoreProperties
     */
    public function getDublinCoreProperties()
    {
        return $this->dublinCoreProperties;
    }

    /**
     * Returns EXIF schema properties found in the document.
     *
     * @return ExifProperties
     */
    public function getExifProperties()
    {
        return $this->exifProperties;
    }
}
