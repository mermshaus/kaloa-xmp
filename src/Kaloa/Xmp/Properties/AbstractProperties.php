<?php
/**
 * Kaloa Library (http://www.kaloa.org/)
 *
 * @license http://www.kaloa.org/license.txt MIT License
 */

namespace Kaloa\Xmp\Properties;

use DOMXPath;

/**
 *
 */
abstract class AbstractProperties
{
    protected $xPath;

    public function __construct(DOMXPath $xPath)
    {
        $this->xPath = $xPath;

        $this->init();
    }

    abstract protected function init();
}
