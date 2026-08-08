<?php

namespace Famoser\Elliptic\Math\Calculator;

/**
 * @template T
 */
interface CalculatorInterface
{
    /**
     * multiplies by factor
     *
     * @param T $point
     * @param \GMP $factor
     * @return T
     */
    public function mul(mixed $point, \GMP $factor): mixed;

    /**
     * calculates the power of two
     *
     *  e.g., (P, 1) -> 2^1 * P = P + P
     *  e.g., (P, 2) -> 2^2 * P = P + P + P + P
     *
     * @param T $point
     * @return T
     */
    public function pow2(mixed $point, int $powerOfTwo): mixed;

    /**
     * multiplies by the cofactor
     *
     * @param T $point
     * @return T
     */
    public function mulH(mixed $point): mixed;
}
