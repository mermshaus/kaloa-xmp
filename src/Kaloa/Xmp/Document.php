<?php

namespace Kaloa\Xmp;

use DateTime;
use DOMDocument;
use DOMNode;
use DOMXPath;

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
    protected $dom;

    /**
     * Document's tags
     *
     * @var array $tags
     */
    protected $tags;

    /**
     * DC title from XMP data
     *
     * @var string $title
     */
    protected $title;

    /**
     * Supposed to reflect creation time of associated resource
     *
     * @var DateTime $exifDateTimeOriginal
     */
    protected $exifDateTimeOriginal;

    /**
     *
     * @param DOMDocument $dom
     */
    public function __construct(DOMDocument $dom)
    {
        $this->dom   = $dom;
        $this->tags  = array();
        $this->title = '';
        $this->exifDateTimeOriginal = '';

        $this->process();
    }

    /**
     * Compiles raw XMP XML data into a format easier to work with
     */
    protected function process()
    {
        $xp = new DOMXPath($this->dom);
        $xp->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');
        $xp->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xp->registerNamespace('exif', 'http://ns.adobe.com/exif/1.0/');

        foreach ($xp->query('//dc:title//rdf:li') as $node) {
            $this->title = $node->nodeValue;
        }

        foreach ($xp->query('//rdf:Description') as $node) {
            /* @var $node DOMNode */
            if ($node->hasAttributes()) {
                $attribute = $node->attributes->getNamedItemNS(
                        'http://ns.adobe.com/exif/1.0/', 'DateTimeOriginal');

                $this->exifDateTimeOriginal = ($attribute !== null)
                        ? DateTime::createFromFormat('U', strtotime($attribute->nodeValue))
                        : null;
            }
        }

        foreach ($xp->query('//dc:subject//rdf:li') as $node) {
            $this->tags[] = $node->nodeValue;
        }
    }

    /**
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return int
     */
    public function getExifDateTimeOriginal()
    {
        return $this->exifDateTimeOriginal;
    }
}
