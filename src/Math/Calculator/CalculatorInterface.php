<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Provides double and add using some native representation (e.g. Jacobi).
 *
 * @template T
 */
interface CalculatorInterface
{
    /**
     * @return T
     */
    public function affineToNative(Point $point): mixed;

    /**
     * @param T $nativePoint
     */
    public function nativeToAffine(mixed $nativePoint): Point;

    /**
     * @return T
     */
    public function getNativeInfinity(): mixed;

    public function getCurve(): Curve;

    /**
     * @param T $a
     * @param T $b
     * @return T
     */
    public function add(mixed $a, mixed $b): mixed;

    /**
     * @param T $a
     * @return T
     */
    public function double(mixed $a): mixed;

    /**
     * @param T $a
     * @param T $b
     */
    public function conditionalSwap(mixed $a, mixed $b, int $swapBit): void;
}
