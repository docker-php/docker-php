<?php

namespace Docker\Http\Stream\Filter;

use GuzzleHttp\Event\HasEmitterInterface;

class Event extends \php_user_filter implements HasEmitterInterface
{
    /**
     * @var \GuzzleHttp\Event\EmitterInterface
     */
    private $emitter;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string Buffer when having vnd docker raw stream
     */
    private $buffer = "";

    /**
     * {@inheritDoc}
     */
    public function getEmitter()
    {
        return $this->emitter;
    }

    /**
     * Function call when stream is filtered
     *
     * @param resource $in       Input stream
     * @param resource $out      Output stream
     * @param integer  $consumed Data consumed
     * @param boolean  $closing  Whether the stream is closing
     *
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        $bucket = stream_bucket_make_writeable($in);

        if (null == $bucket) {
            return PSFS_PASS_ON;
        }

        $consumed = $bucket->datalen;
        stream_bucket_append($out, $bucket);

        $this->buffer .= $bucket->data;

        $data = $this->buffer;
        $type = null;

        if ($this->contentType == "application/vnd.docker.raw-stream") {
            if (strlen($this->buffer) < 8) {
                return PSFS_FEED_ME;
            }

            $header  = substr($this->buffer, 0, 8);
            $decoded = unpack('C1stream_type/C3/N1size', $header);

            if (strlen($this->buffer) < (8 + $decoded['size'])) {
                return PSFS_FEED_ME;
            }

            $data         = substr($this->buffer, 8, $decoded['size']);
            $type         = $decoded['stream_type'];
            $this->buffer = substr($this->buffer, 8 + $decoded['size']);
        } else {
            $this->buffer = "";
        }

        if ($this->contentType == "application/json") {
            $data = json_decode($data, true);
        }

        if (!empty($data)) {
            $this->getEmitter()->emit('response.output', new OutputEvent($data, $type));
        }

        return PSFS_PASS_ON;
    }

    /**
     * Call when filter is created (attache to a socket)
     *
     * Here we set parameters to this instance
     */
    public function onCreate()
    {
        $this->emitter = $this->params['emitter'];
        $this->contentType = $this->params['content_type'];
    }
}
