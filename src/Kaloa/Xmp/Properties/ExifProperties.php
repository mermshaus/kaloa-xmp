<?php
/**
 * Kaloa Library (http://www.kaloa.org/)
 *
 * @license http://www.kaloa.org/license.txt MIT License
 */

namespace Kaloa\Xmp\Properties;

use DateTime;
use DOMNode;

use Kaloa\Xmp\Properties\AbstractProperties;

/**
 * Extracts the Exif properties from an XMP document referenced by an XPath
 * instance.
 *
 * The descriptions for all getters are taken from
 * <a href="http://www.exiv2.org/tags-xmp-exif.html">exiv2.org</a>.
 *
 * @todo More EXIF properties will be added in later versions.
 */
class ExifProperties extends AbstractProperties
{
    /**
     * See getDateTimeOriginal.
     *
     * @var DateTime|null
     */
    private $dateTimeOriginal = null;

    /**
     * See getExifVersion.
     *
     * @var string
     */
    private $exifVersion = '';

    /**
     * See getPixelXDimension.
     *
     * @var string
     */
    private $pixelXDimension = '';

    /**
     * See getPixelYDimension.
     *
     * @var string
     */
    private $pixelYDimension = '';

    /**
     * Tries to fill an entity instance variable with corresponding data from
     * the XMP document.
     *
     * Exif data might be added either as attributes or as independent elements.
     * This method checks for both. If both types are found, element content
     * will overwrite attribute content.
     *
     * @param string $entity
     */
    private function fill($entity)
    {
        $value = '';
        $whatLcfirst = lcfirst($entity);

        foreach ($this->xPath->query('//rdf:Description') as $node) {
            /* @var $node DOMNode */
            if ($node->hasAttributes()) {
                $attribute = $node->attributes->getNamedItemNS(
                    'http://ns.adobe.com/exif/1.0/',
                    $entity
                );

                if ($attribute !== null) {
                    $value = $attribute->nodeValue;
                }
            }
        }

        if ($value === '') {
            foreach ($this->xPath->query('//exif:' . $entity) as $node) {
                $value = $node->nodeValue;
            }
        }

        $this->{$whatLcfirst} = $value;
    }

    /**
     * Retrieves all properties from the underlying XMP document.
     */
    final protected function init()
    {
        $this->fill('DateTimeOriginal');

        if (trim($this->dateTimeOriginal) !== '') {
            $this->dateTimeOriginal = DateTime::createFromFormat(
                'Y-m-d\TH:i:s.uP',
                $this->dateTimeOriginal
            );
        } else {
            $this->dateTimeOriginal = null;
        }

        $this->fill('ExifVersion');
        $this->fill('PixelXDimension');
        $this->fill('PixelYDimension');

        // Free our reference to the XPath instance.
        $this->xPath = null;
    }

    /**
     * Returns date and time when original image was generated, in ISO 8601
     * format.
     *
     * @return DateTime|null
     */
    public function getDateTimeOriginal()
    {
        return $this->dateTimeOriginal;
    }

    /**
     * Returns the EXIF version number.
     *
     * @return string
     */
    public function getExifVersion()
    {
        return $this->exifVersion;
    }

    /**
     * Return the image width, in pixels.
     *
     * @return string
     */
    public function getPixelXDimension()
    {
        return $this->pixelXDimension;
    }

    /**
     * Returns the image height, in pixels.
     *
     * @return string
     */
    public function getPixelYDimension()
    {
        return $this->pixelYDimension;
    }
}
