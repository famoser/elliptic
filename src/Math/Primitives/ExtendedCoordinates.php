<?php

namespace Famoser\Elliptic\Math\Primitives;

class ExtendedCoordinates
{
    public function __construct(public \GMP $X, public \GMP $Y, public \GMP $Z, public \GMP $T)
    {
    }

    public static function createInfinity(): ExtendedCoordinates
    {
        return new ExtendedCoordinates(gmp_init(0), gmp_init(1), gmp_init(1), gmp_init(0));
    }

    public function isInfinity(): bool
    {
        return gmp_cmp($this->Y, $this->Z) === 0 && gmp_cmp($this->Y, 0) !== 0;
    }

    public function equals(ExtendedCoordinates $other): bool
    {
        return gmp_cmp($this->X, $other->X) === 0 && gmp_cmp($this->Z, $other->Z) === 0;
    }
}
