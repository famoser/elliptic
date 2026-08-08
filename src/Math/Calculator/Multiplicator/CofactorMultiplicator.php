<?php

namespace Famoser\Elliptic\Math\Calculator\Multiplicator;

/**
 * @template T
 */
trait CofactorMultiplicator
{
    /**
     * multiplies by the cofactor
     *
     * @param T $point
     * @return T
     */
    public function mulH(mixed $point): mixed
    {
        $cofactorPow2 = gmp_scan1($this->curve->getH(), 0);
        assert(gmp_scan1($this->curve->getH(), $cofactorPow2 + 1) === -1);

        return $this->pow2($point, $cofactorPow2);
    }

    /**
     * calculates the power of two
     *
     *  e.g., (P, 1) -> 2^1 * P = P + P
     *  e.g., (P, 2) -> 2^2 * P = P + P + P + P
     *
     * @param T $point
     * @return T
     */
    public function pow2(mixed $point, int $powerOfTwo): mixed
    {
        $res = $point;
        for ($i = 0; $i < $powerOfTwo; $i++) {
            $res = $this->double($res);
        }

        return $res;
    }
}
