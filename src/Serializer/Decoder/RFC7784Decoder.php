<?php

namespace Famoser\Elliptic\Serializer\Decoder;

/**
 * implements the decoding described in RFC 7784
 */
class RFC7784Decoder
{
    private readonly BinaryDecoder $decoder;

    public function __construct() {
        $this->decoder = new BinaryDecoder();
    }

    public function decodeUCoordinate(string $uHex, int $bits): \GMP
    {
        $u_list = $this->decoder->decodeHexToByteArray($uHex);

        // Ignore any unused bits
        if ($bits % 8) {
            $u_list[count($u_list) - 1] &= (1 << ($bits % 8)) - 1;
        }

        return $this->decoder->decodeLittleEndian($u_list, $bits);
    }

    public function encodeUCoordinate(\GMP $b, int $bits): string
    {
        $u_list = $this->decoder->encodeLittleEndian($b, $bits);

        // Ignore any unused bits
        if ($bits % 8) {
            $u_list[count($u_list) - 1] &= (1 << ($bits % 8)) - 1;
        }

        return $this->decoder->encodeByteArrayToHex($u_list);
    }

    public function decodeScalar25519(string $k): \GMP
    {
        $k_list = $this->decoder->decodeHexToByteArray($k);

        // Apply the bit masks
        $k_list[0] &= 248;
        $k_list[31] &= 127;
        $k_list[31] |= 64;

        return $this->decoder->decodeLittleEndian($k_list, 255);
    }

    public function decodeScalar448(string $k): \GMP
    {
        $k_list = $this->decoder->decodeHexToByteArray($k);

        // Apply the bit masks
        $k_list[0] &= 252;
        $k_list[55] |= 128;

        return $this->decoder->decodeLittleEndian($k_list, 448);
    }
}
