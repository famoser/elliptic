<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

abstract class AbstractMath implements MathInterface
{
    public function __construct(protected readonly Curve $curve)
    {
    }

    public function getCurve(): Curve
    {
        return $this->curve;
    }

    public function mulG(\GMP $factor): Point
    {
        /**
         * Optimization potential here:
         * - For window (e.g. of size 4 bits), precompute G into table t. Then, Q <- 2Q; Q <- Q + t[window]
         * - Generalize above method, precompute full table (to also avoid doubling)
         *
         * But needs to be measured whether actually some advantage, as:
         * - Table needs to be fully traversed, else private key imprinted in cache
         * - Especially costly as above implied swapping two values (x,y) repeatedly
         * - Maybe faster by encoding table as string, and only generating chosen gmp afterwards?
         * - Might introduce additional side-channels
         */
        return $this->mul($this->curve->getG(), $factor);
    }
}
