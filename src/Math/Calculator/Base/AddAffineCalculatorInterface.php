<?php

namespace Famoser\Elliptic\Math\Calculator\Base;

use Famoser\Elliptic\Primitives\Point;

/**
 * Provides affine add, which sometimes improves performance (e.g. when used in conjunction with fixed-based multiplication).
 *
 * @template T
 */
interface AddAffineCalculatorInterface extends CalculatorInterface
{
    /**
     * @param T $a
     * @param Point $b
     * @return T
     */
    public function addAffine(mixed $a, Point $b): mixed;
}
