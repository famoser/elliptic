<?php

namespace Famoser\Elliptic\Math\Primitives;

class XZPoint
{
    public function __construct(public \GMP $X, public \GMP $Z)
    {
    }

    public static function createInfinity(): XZPoint
    {
        return new XZPoint(gmp_init(0), gmp_init(0));
    }

    public function isInfinity(): bool
    {
        return gmp_cmp($this->Z, 0) === 0;
    }

    public function equals(XZPoint $other): bool
    {
        return gmp_cmp($this->X, $other->X) === 0 && gmp_cmp($this->Z, $other->Z) === 0;
    }
}
