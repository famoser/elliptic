<?php

namespace Famoser\Elliptic\Primitives;

/**
 * Elliptic curve point with x and y coordinates
 */
class Point
{
    public function __construct(public \GMP $x, public \GMP $y)
    {
    }

    public function equals(self $other): bool
    {
        return gmp_cmp($this->x, $other->x) === 0 && gmp_cmp($this->y, $other->y) === 0;
    }
}
