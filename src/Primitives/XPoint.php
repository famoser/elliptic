<?php

namespace Famoser\Elliptic\Primitives;

/**
 * Elliptic curve point with x and y coordinates, but the y coordinate is implicit
 */
class XPoint
{
    public function __construct(public \GMP $x)
    {
    }

    public static function createInfinity(): XPoint
    {
        return new XPoint(gmp_init(0));
    }

    public function isInfinity(): bool
    {
        return gmp_cmp($this->x, 0) === 0;
    }

    public function equals(self $other): bool
    {
        return gmp_cmp($this->x, $other->x) === 0;
    }
}
