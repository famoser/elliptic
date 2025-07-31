<?php

namespace Famoser\Elliptic\Serializer\Decoder;

/**
 * implements the decoding described in RFC 7784
 */
class BinaryDecoder
{
    /**
     * @return int[]
     */
    public function decodeHexToByteArray(string $hex): array
    {
        // if you pass in non-hex, php will throw a warning. hence no error checking on our side.
        /** @phpstan-ignore-next-line */
        return array_values(unpack('C*', hex2bin($hex)));
    }

    /**
     * @param int[] $b
     */
    public function encodeByteArrayToHex(array $b): string
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
    public function decodeLittleEndian(array $b, int $bits): \GMP
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
    public function encodeLittleEndian(\GMP $b, int $bits): array
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
}
