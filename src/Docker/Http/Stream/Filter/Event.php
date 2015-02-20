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
     * {@inheritdoc}
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

        $data = $this->buffer . $bucket->data;
        $type = null;

        if ($this->contentType == "application/vnd.docker.raw-stream") {
            if (strlen($data) < 8) {
                $this->buffer = $data;

                return PSFS_FEED_ME;
            }

            $header  = substr($data, 0, 8);
            $decoded = unpack('C1stream_type/C3/N1size', $header);

            if (strlen($data) < (8 + $decoded['size'])) {
                $this->buffer = $data;

                return PSFS_FEED_ME;
            }

            $data         = substr($data, 8, $decoded['size']);
            $type         = $decoded['stream_type'];
            $this->buffer = substr($data, 8 + $decoded['size']);

            if (!empty($data)) {
                $this->getEmitter()->emit('response.output', new OutputEvent($data, $type));
            }

            return PSFS_PASS_ON;
        }

        if ($this->contentType == "application/json") {
            foreach ($this->jsonSplitDecode($data) as $data) {
                $this->getEmitter()->emit('response.output', new OutputEvent($data, $type));
            }

            return PSFS_PASS_ON;
        }

        if (!empty($data)) {
            $this->getEmitter()->emit('response.output', new OutputEvent($data, $type));
        }

        return PSFS_PASS_ON;
    }

    /**
     * Call when filter is created (attach to a socket)
     *
     * Here we set parameters to this instance
     */
    public function onCreate()
    {
        $this->emitter = $this->params['emitter'];
        $this->contentType = $this->params['content_type'];
    }

    private function jsonSplitDecode($json)
    {
        $splited = [];
        $size    = strlen($json);
        $inquote = false;

        for ($level = 0, $objects = 0, $i =0; $i < $size; $i++) {
            if ((boolean)($json[$i] == '"' && ($i > 0 ? $json[$i-1] : '') != '\\')) {
                $inquote = !$inquote;
            }

            if (!$inquote && in_array($json[$i], [" ", "\r", "\n", "\t"])) {
                continue;
            }

            if (!$inquote && in_array($json[$i], ['{', '['])) {
                $level++;
            }

            if (!$inquote && in_array($json[$i], ['}', ']'])) {
                $level--;

                if ($level == 0) {
                    $splited[$objects] .= $json[$i];
                    $objects++;
                    continue;
                }
            }

            if (!isset($splited[$objects])) {
                $splited[$objects] = "";
            }

            $splited[$objects] .= $json[$i];
        }

        foreach ($splited as $key => $jsonString) {
           $splited[$key] = json_decode($jsonString, true);
        }

        return $splited;
    }
}
