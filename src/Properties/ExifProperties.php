<?php

declare(strict_types=1);

/*
 * This file is part of the kaloa/xmp package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Xmp\Properties;

use DateTime;
use DateTimeInterface;
use DOMNode;

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
     */
    private ?DateTime $dateTimeOriginal = null;

    /**
     * See getExifVersion.
     */
    private string $exifVersion = '';

    /**
     * See getPixelXDimension.
     */
    private string $pixelXDimension = '';

    /**
     * See getPixelYDimension.
     */
    private string $pixelYDimension = '';

    private function read(string $entity): string
    {
        $value = '';

        foreach ($this->xPath->query('//rdf:Description') as $node) {
            /* @var $node DOMNode */
            if ($node->hasAttributes()) {
                $attribute = $node->attributes->getNamedItemNS('http://ns.adobe.com/exif/1.0/', $entity);

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

        return $value;
    }

    /**
     * Tries to fill an entity instance variable with corresponding data from
     * the XMP document.
     *
     * Exif data might be added either as attributes or as independent elements.
     * This method checks for both. If both types are found, element content
     * will overwrite attribute content.
     */
    private function fill(string $entity): void
    {
        $whatLcfirst = lcfirst($entity);

        $value = $this->read($entity);

        $this->{$whatLcfirst} = $value;
    }

    /**
     * Retrieves all properties from the underlying XMP document.
     */
    final protected function init(): void
    {
        $dateTimeOriginalRaw = $this->read('DateTimeOriginal');

        if (trim($dateTimeOriginalRaw) !== '') {
            $this->dateTimeOriginal = DateTime::createFromFormat('Y-m-d\\TH:i:s.uP', $dateTimeOriginalRaw);
        } else {
            $this->dateTimeOriginal = null;
        }

        $this->fill('ExifVersion');
        $this->fill('PixelXDimension');
        $this->fill('PixelYDimension');
    }

    /**
     * Returns date and time when original image was generated.
     */
    public function getDateTimeOriginal(): ?DateTimeInterface
    {
        return $this->dateTimeOriginal;
    }

    /**
     * Returns the EXIF version number.
     */
    public function getExifVersion(): string
    {
        return $this->exifVersion;
    }

    /**
     * Return the image width, in pixels.
     */
    public function getPixelXDimension(): string
    {
        return $this->pixelXDimension;
    }

    /**
     * Returns the image height, in pixels.
     */
    public function getPixelYDimension(): string
    {
        return $this->pixelYDimension;
    }
}
