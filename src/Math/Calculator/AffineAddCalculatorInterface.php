<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Primitives\Point;

/**
 * @template T
 */
interface AffineAddCalculatorInterface
{
    /**
     * @param T $a
     * @param Point $b
     * @return T
     */
    public function addAffine(mixed $a, Point $b): mixed;
}
