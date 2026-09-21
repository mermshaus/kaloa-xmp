<?php

declare(strict_types=1);

/*
 * This file is part of the kaloa/xmp package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Xmp;

use DOMDocument;
use ErrorException;
use Exception;
use Kaloa\Xmp\Document as XmpDocument;
use RuntimeException;

/**
 * Extracts an XMP document from a data stream.
 *
 * The current algorithm ignores specific features and requirements of file
 * formats. It simply looks for the first occurrences of $tokenStart and
 * $tokenEnd and returns the content in between. This is a flexible approach but
 * it is not a correct one. There are cases in which the algorithm won't
 * succeed. Both false positives and false negatives are possible.
 */
class Reader
{
    /**
     * Start token of XMP data.
     */
    private const TOKEN_START = '<x:xmpmeta';

    /**
     * End token of XMP data.
     */
    private const TOKEN_END = '</x:xmpmeta>';

    /**
     * Size (in bytes) of data chunks read from the stream.
     *
     * @var positive-int
     */
    private const CHUNK_SIZE = 1024;

    /**
     * Buffer to construct XMP data in.
     */
    private string $buffer;

    /**
     * True if $tokenStart has been found.
     */
    private bool $started;

    /**
     * True if $started and $tokenEnd has been found.
     */
    private bool $ended;

    /**
     * Counts how many characters of the token that is currently searched for
     * have been found.
     *
     * This is reset whenever a character that doesn't equal the next one from
     * the token is found. If $delimPos reaches token length, the token has been
     * found.
     *
     * This variable is needed because a token might be split over two chunks of
     * input data so that functions such as strpos aren't sufficient.
     */
    private int $delimPos;

    /**
     * Length (in byte) of $tokenStart.
     */
    private int $tokenStartLen;

    /**
     * Length (in byte) of $tokenEnd.
     */
    private int $tokenEndLen;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * Resets instance data to clean starting state.
     */
    private function reset(): void
    {
        $this->buffer = '';
        $this->started = false;
        $this->ended = false;
        $this->tokenStartLen = strlen(self::TOKEN_START);
        $this->tokenEndLen = strlen(self::TOKEN_END);
        $this->delimPos = 0;
    }

    /**
     * Searches incoming data for $tokenStart adapting internal state if found.
     *
     * @param string $char A single byte
     */
    private function searchForTokenStart(string $char): void
    {
        if ($char === self::TOKEN_START[$this->delimPos]) {
            $this->delimPos++;
            if ($this->delimPos === $this->tokenStartLen) {
                $this->delimPos = 0;
                $this->started = true;
            }
        } elseif ($char === self::TOKEN_START[0]) {
            $this->delimPos = 1;
        } else {
            $this->delimPos = 0;
        }
    }

    /**
     * Searches incoming data for $tokenEnd adapting internal state if found.
     *
     * @param string $char A single byte
     */
    private function searchForTokenEnd(string $char): void
    {
        $this->buffer .= $char;
        if ($char === self::TOKEN_END[$this->delimPos]) {
            $this->delimPos++;
            if ($this->delimPos === $this->tokenEndLen) {
                $this->ended = true;
            }
        } elseif ($char === self::TOKEN_END[0]) {
            $this->delimPos = 1;
        } else {
            $this->delimPos = 0;
        }
    }

    /**
     * Extracts the first found XMP document from the stream.
     *
     * The stream is read in chunks and processed byte by byte in an
     * automaton-like fashion.
     *
     * After the execution of this method, instance variables $buffer, $started,
     * and $ended will contain meaningful values.
     *
     * @param resource $stream A stream resource
     */
    private function getXmpData($stream): void
    {
        while (!feof($stream)) {
            $chunk = fread($stream, self::CHUNK_SIZE);

            if ($chunk === false) {
                throw new RuntimeException('Unable to read data');
            }

            foreach (str_split($chunk) as $char) {
                if (!$this->started) {
                    $this->searchForTokenStart($char);
                } else {
                    $this->searchForTokenEnd($char);
                }

                if ($this->ended) {
                    break 2;
                }
            }
        }

        if ($this->started && $this->ended) {
            $this->buffer = self::TOKEN_START . $this->buffer;
        } else {
            $this->buffer = '';
        }
    }

    /**
     * Returns a Kaloa\Xmp\Document of the first occurrence of XMP data in the
     * stream.
     *
     * @todo The method of error handling (set_error_handler) is just insane.
     *
     * @param resource $stream A stream resource
     *
     * @throws ReaderException
     */
    public function getXmpDocument($stream): XmpDocument
    {
        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new ReaderException('$stream is not a valid stream resource');
        }

        $this->getXmpData($stream);

        if ($this->buffer === '') {
            $this->reset();
            throw new ReaderException('No XMP document found in stream');
        }

        set_error_handler(static function ($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        });

        try {
            $dom = new DOMDocument();
            $ret = $dom->loadXML($this->buffer);

            // Added to make testErroneousXmpDataThrowsException work with hhvm
            if ($ret === false) {
                throw new RuntimeException('loadXML returned false.');
            }
        } catch (Exception $e) {
            // Finally
            restore_error_handler();
            $this->reset();

            throw new ReaderException($e->getMessage());
        }

        // Finally
        restore_error_handler();
        $this->reset();

        return new XmpDocument($dom);
    }
}
