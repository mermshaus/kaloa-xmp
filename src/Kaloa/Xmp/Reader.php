<?php

namespace Kaloa\Xmp;

use DOMDocument;
use RuntimeException;
use Kaloa\Xmp\ReaderException;

/**
 *
 */
class Reader
{
    /**
     *
     * @param string $filename
     * @return string
     */
    protected function getXmpData($filename)
    {
        if (!is_readable($filename)) {
            throw new RuntimeException('Could not open file for reading');
        }

        if (($file_pointer = fopen($filename, 'r')) === FALSE) {
            throw new RuntimeException('Could not open file for reading (should be readable though)');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ('image/jpeg' !== finfo_file($finfo, $filename)) {
            finfo_close($finfo);
            throw new RuntimeException('File is not image/jpeg');
        }
        finfo_close($finfo);

        $from = '<x:xmpmeta';
        $to   = '</x:xmpmeta>';

        $chunk_size = 1024;
        $buffer = '';
        $started = false;
        $ended = false;

        $fromLen = strlen($from);
        $toLen = strlen($to);

        $delimPos = 0;

        while (!feof($file_pointer)) {
            $chunk = fread($file_pointer, $chunk_size);
            foreach (str_split($chunk) as $char) {
                if (!$started) {
                    if ($char === $from[$delimPos]) {
                        $delimPos++;
                        if ($delimPos === $fromLen) {
                            $delimPos = 0;
                            $started = true;
                        }
                    } else if ($char === $from[0]) {
                        $delimPos = 1;
                    } else {
                        $delimPos = 0;
                    }
                } else {
                    $buffer .= $char;
                    if ($char === $to[$delimPos]) {
                        $delimPos++;
                        if ($delimPos === $toLen) {
                            $ended = true;
                            break 2;
                        }
                    } else if ($char === $to[0]) {
                        $delimPos = 1;
                    } else {
                        $delimPos = 0;
                    }
                }
            }
        }

        if ($started && $ended) {
            $buffer = $from . $buffer;
        } else {
            $buffer = '';
        }

        fclose($file_pointer);

        return $buffer;
    }

    /**
     * Returns XMP data
     *
     * @param string $filename
     * @return DOMDocument
     * @throws ReaderException
     */
    public function getXmpDocument($filename)
    {
        $rawXmp = $this->getXmpData($filename);

        #echo '<pre>' . htmlspecialchars($rawXmp) . '</pre>';

        if ($rawXmp === '') {
            throw new ReaderException('Document is not set');
        }

        $dom = new DOMDocument();
        $dom->loadXML($rawXmp);

        $xmpDoc = new Document($dom);

        return $xmpDoc;
    }
}
