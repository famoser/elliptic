<?php

namespace Famoser\Elliptic\Math\Calculator\Swapper;

trait ScalarSwapper
{
    public function conditionalSwapScalar(\GMP &$a, \GMP &$b, int $swapBit, int $maxBitLength): void
    {
        // create a mask (note how it inverts the maskbit)
        $mask = gmp_init(str_repeat((string)(1 - $swapBit), $maxBitLength), 2);

        // if mask is 1, tempA = a, else temp = 0
        $tempA = gmp_and($a, $mask);
        $tempB = gmp_and($b, $mask);

        $a = gmp_xor($tempB, gmp_xor($a, $b)); // if mask is 1, then b XOR a XOR b = a, else 0 XOR a XOR b = a XOR b
        $b = gmp_xor($tempA, gmp_xor($a, $b)); // if mask is 1, then a XOR a XOR b = b, else 0 XOR a XOR b XOR b = a
        $a = gmp_xor($tempB, gmp_xor($a, $b)); // if mask is 1, then b XOR a XOR b = a, else 0 XOR a XOR b XOR a = b

        // hence if mask is 1 (= inverse of $swapBit), then no swap, else swap
    }
}
