<?php
/**
 * Kaloa Library (http://www.kaloa.org/)
 *
 * @license http://www.kaloa.org/license.txt MIT License
 */

namespace Kaloa\Xmp;

use DOMDocument;
use ErrorException;
use Exception;

use Kaloa\Xmp\Document as XmpDocument;
use Kaloa\Xmp\ReaderException;

/**
 *
 */
class Reader
{
    private $tokenFrom = '<x:xmpmeta';
    private $tokenTo   = '</x:xmpmeta>';
    private $chunkSize = 1024;
    private $buffer;
    private $started;
    private $ended;
    private $delimPos;
    private $fromLen;
    private $toLen;

    public function __construct()
    {
        $this->reset();
    }

    private function reset()
    {
        $this->buffer = '';
        $this->started = false;
        $this->ended = false;
        $this->fromLen = strlen($this->tokenFrom);
        $this->toLen = strlen($this->tokenTo);
        $this->delimPos = 0;
    }

    private function notStarted($char)
    {
        if ($char === $this->tokenFrom[$this->delimPos]) {
            $this->delimPos++;
            if ($this->delimPos === $this->fromLen) {
                $this->delimPos = 0;
                $this->started = true;
            }
        } elseif ($char === $this->tokenFrom[0]) {
            $this->delimPos = 1;
        } else {
            $this->delimPos = 0;
        }
    }

    private function started($char)
    {
        $this->buffer .= $char;
        if ($char === $this->tokenTo[$this->delimPos]) {
            $this->delimPos++;
            if ($this->delimPos === $this->toLen) {
                $this->ended = true;
            }
        } elseif ($char === $this->tokenTo[0]) {
            $this->delimPos = 1;
        } else {
            $this->delimPos = 0;
        }
    }

    /**
     *
     * @param resource $stream A stream resource
     * @return string
     */
    private function getXmpData($stream)
    {
        while (!feof($stream)) {
            $chunk = fread($stream, $this->chunkSize);

            foreach (str_split($chunk) as $char) {
                if (!$this->started) {

                    $this->notStarted($char);

                } else {
                    $this->started($char);

                    if ($this->ended) {
                        break 2;
                    }
                }
            }
        }

        if ($this->started && $this->ended) {
            $this->buffer = $this->tokenFrom . $this->buffer;
        } else {
            $this->buffer = '';
        }
    }

    /**
     * Returns XMP data
     *
     * @todo The method of error handling (set_error_handler) is just insane.
     *
     * @param resource $stream A stream resource
     * @return XmpDocument
     * @throws ReaderException
     */
    public function getXmpDocument($stream)
    {
        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new ReaderException('$stream is not a valid stream resource');
        }

        $this->getXmpData($stream);

        if ($this->buffer === '') {
            $this->reset();
            throw new ReaderException('No XMP document found in stream');
        }


        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        });

        try {
            $dom = new DOMDocument();
            $dom->loadXML($this->buffer);
        } catch (Exception $e) {
            // Finally
            restore_error_handler();
            $this->reset();

            throw new ReaderException($e->getMessage());
        }

        // Finally
        restore_error_handler();
        $this->reset();


        $xmpDoc = new XmpDocument($dom);

        return $xmpDoc;
    }
}
