<?php

namespace Famoser\Elliptic\Serializer\Decoder;

/**
 * implements the decoding described in RFC 7784
 */
class RFC7784Decoder
{
    /**
     * @return int[]
     */
    private function decodeHexToBytes(string $hex): array
    {
        $cleanedHex = preg_replace('/\s+/', '', $hex);

        /** @phpstan-ignore-next-line */
        $list = unpack('C*', hex2bin($cleanedHex));

        /** @var int[]|false $list */
        if (!$list) {
            throw new \InvalidArgumentException('Invalid hex string');
        }

        return array_values($list);
    }

    /**
     * @param int[] $b
     */
    private function encodeBytesToHex(array $b): string
    {
        $result = '';
        foreach ($b as $entry) {
            $result .= bin2hex(pack('C', $entry));
        }

        return $result;
    }

    /**
     * @param int[] $b
     */
    private function decodeLittleEndian(array $b, int $bits): \GMP
    {
        $sum = gmp_init(0);
        $bytes = intdiv($bits + 7, 8);

        for ($i = 0; $i < $bytes; $i++) {
            $value = gmp_mul(gmp_init($b[$i]), gmp_pow(2, 8 * $i));
            $sum = gmp_add($sum, $value);
        }

        return $sum;
    }

    /**
     * @return int[]
     */
    private function encodeLittleEndian(\GMP $b, int $bits): array
    {
        $result = [];

        $mask = gmp_init(0xFF);
        $number = $b;
        $bytes = intdiv($bits + 7, 8);
        for ($i = 0; $i < $bytes; $i++) {
            $result[] = gmp_intval(gmp_and($number, $mask));
            $number = gmp_div($number, gmp_pow(2, 8));
        }

        return $result;
    }

    public function decodeUCoordinate(string $uHex, int $bits): \GMP
    {
        $u_list = $this->decodeHexToBytes($uHex);

        // Ignore any unused bits
        if ($bits % 8) {
            $u_list[count($u_list) - 1] &= (1 << ($bits % 8)) - 1;
        }

        return $this->decodeLittleEndian($u_list, $bits);
    }

    public function encodeUCoordinate(\GMP $b, int $bits): string
    {
        $u_list = $this->encodeLittleEndian($b, $bits);

        // Ignore any unused bits
        if ($bits % 8) {
            $u_list[count($u_list) - 1] &= (1 << ($bits % 8)) - 1;
        }

        return $this->encodeBytesToHex($u_list);
    }

    public function decodeScalar25519(string $k): \GMP
    {
        $k_list = $this->decodeHexToBytes($k);

        // Apply the bit masks
        $k_list[0] &= 248;
        $k_list[31] &= 127;
        $k_list[31] |= 64;

        return $this->decodeLittleEndian($k_list, 255);
    }

    public function decodeScalar448(string $k): \GMP
    {
        $k_list = $this->decodeHexToBytes($k);

        // Apply the bit masks
        $k_list[0] &= 252;
        $k_list[55] |= 128;

        return $this->decodeLittleEndian($k_list, 448);
    }
}
