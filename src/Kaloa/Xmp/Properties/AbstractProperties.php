<?php
/**
 * Kaloa Library (http://www.kaloa.org/)
 *
 * @license http://www.kaloa.org/license.txt MIT License
 */

namespace Kaloa\Xmp\Properties;

use DOMXPath;

/**
 * Shared base class for XMP property schemata.
 */
abstract class AbstractProperties
{
    /**
     * XPath instance for a Kaloa\Xmp\Document.
     *
     * @var DOMXPath
     */
    protected $xPath;

    /**
     * Initializes the instance.
     *
     * @param DOMXPath $xPath XPath instance for a Kaloa\Xmp\Document
     */
    public function __construct(DOMXPath $xPath)
    {
        $this->xPath = $xPath;

        $this->init();
    }

    /**
     * Implementing classes should place initialization code here rather than
     * overwriting __construct.
     */
    abstract protected function init();
}
