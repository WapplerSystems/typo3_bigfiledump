<?php

declare(strict_types=1);

namespace WapplerSystems\Bigfiledump\Core\Http;

use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;
use TYPO3\CMS\Core\Http\SelfEmittableStreamInterface;

/**
 * This class implements a stream that can be used like a usual PSR-7 stream
 * but is additionally able to provide a file-serving fastpath using readfile().
 * The file this stream refers to is opened on demand.
 *
 */
class SelfEmittableLazyOpenStream implements SelfEmittableStreamInterface
{
    use StreamDecoratorTrait;
    protected string $filename;
    protected StreamInterface $stream;

    /**
     * Constructor setting up the PHP resource
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->stream = new LazyOpenStream($filename, 'r');
        $this->filename = $filename;
    }

    /**
     * Output the contents of the file to the output buffer
     */
    public function emit()
    {
        ob_clean();
        set_time_limit(0);
        $file = @fopen($this->filename, 'rb');
        while (!feof($file)) {
            if (!$file) {
                break;
            }
            print(@fread($file, 1024 * 8));
            ob_flush();
            flush();
        }

    }

    public function isWritable(): bool
    {
        return false;
    }

    /**
     * @param string $string
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        throw new \RuntimeException('Cannot write to a ' . self::class, 1538331833);
    }

    /**
     * Creates the underlying stream lazily when required.
     */
    protected function createStream(): StreamInterface
    {
        return $this->stream;
    }
}
