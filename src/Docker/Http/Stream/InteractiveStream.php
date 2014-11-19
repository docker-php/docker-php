<?php

namespace Docker\Http\Stream;

use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Stream\StreamInterface;

/**
 * An interactive stream is used when communicating with docker container attached
 *
 * It helps dealing with encoding and decoding frame from websocket protcol (hybi 10)
 *
 * @see https://tools.ietf.org/html/rfc6455#section-5.2
 *
 */
class InteractiveStream
{
    private $stream;

    private $socket;

    public function __construct(StreamInterface $stream)
    {
        // Trick to get underlying stream
        $socket = $stream->detach();

        $this->socket = $socket;
        $this->stream = new Stream($socket);
    }

    public function write($data)
    {
        $rand  = rand(0, 28);
        $frame = array(
            'fin'      => 1,
            'rsv1'     => 0,
            'rsv2'     => 0,
            'rsv3'     => 0,
            'opcode'   => 1, // We always send text
            'mask'     => 1,
            'len'      => strlen($data),
            'mask_key' => substr(md5(uniqid()), $rand, 4),
            'data'     => $data,
        );

        if ($frame['mask'] == 1) {
            for ($i = 0; $i < $frame['len']; $i++) {
                $frame['data']{$i}
                = chr(ord($frame['data']{$i}) ^ ord($frame['mask_key']{$i % 4}));
            }
        }

        if ($frame['len'] > pow(2, 16)) {
            $len = 127;
        } elseif ($frame['len'] > 125) {
            $len = 126;
        } else {
            $len = $frame['len'];
        }

        $firstByte  = ($frame['fin'] << 7) | (($frame['rsv1'] << 7) >> 1) | (($frame['rsv2'] << 7) >> 2) | (($frame['rsv3'] << 7) >> 3) | (($frame['opcode'] << 4) >> 4);
        $secondByte = ($frame['mask'] << 7) | (($len << 1) >> 1);

        $this->stream->write(chr($firstByte));
        $this->stream->write(chr($secondByte));

        if ($len == 126) {
            $this->stream->write(pack('n', $frame['len']));
        } elseif ($len == 127) {
            $higher = $frame['len'] >> 32;
            $lower  = ($frame['len'] << 32) >> 32;

            $this->stream->write(pack('N', $higher));
            $this->stream->write(pack('N', $lower));
        }

        if ($frame['mask'] == 1) {
            $this->stream->write($frame['mask_key']);
        }

        $this->stream->write($frame['data']);
    }

    /**
     * Block until it receive a frame from websocket or return null if no more connexion
     *
     */
    public function receive($block = true)
    {
        if ($this->stream->eof()) {
            return null;
        }

        $firstByte = $this->stream->read(1);

        if (!$block && empty($firstByte)) {
            return null;
        }

        if ($block && empty($firstByte)) {
            $firstByte = $this->read(1);
        }

        $frame      = array();
        $firstByte  = ord($firstByte);
        $secondByte = ord($this->read(1));

        // First byte decoding
        $frame['fin']    = ($firstByte & 128) >> 7;
        $frame['rsv1']   = ($firstByte & 64)  >> 6;
        $frame['rsv2']   = ($firstByte & 32)  >> 5;
        $frame['rsv3']   = ($firstByte & 16)  >> 4;
        $frame['opcode'] = ($firstByte & 15);

        // Second byte decoding
        $frame['mask'] = ($secondByte & 128) >> 7;
        $frame['len']  = ($secondByte & 127);

        if ($frame['len'] == 126) {
            $frame['len'] = unpack('n', $this->read(2))[1];
        } elseif ($frame['len'] == 127) {
            list($higher, $lower) = array_values(unpack('N2', $this->read(8)));
            $frame['len'] = ($higher << 32) | $lower;
        }

        if ($frame['mask'] == 1) {
            $frame['mask_key'] = $this->read(4);
        }

        $frame['data'] = $this->read($frame['len']);

        if ($frame['mask'] == 1) {
            for ($i = 0; $i < $frame['len']; $i++) {
                $frame['data']{$i}
                = chr(ord($frame['data']{$i}) ^ ord($frame['mask_key']{$i % 4}));
            }
        }

        return $frame;
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Force to have something of the exepcted size (block)
     *
     * @param $length
     *
     * @return string
     */
    private function read($length)
    {
        $read = "";

        do {
            $read .= $this->stream->read($length - strlen($read));
        } while (strlen($read) < $length);

        return $read;
    }
}
