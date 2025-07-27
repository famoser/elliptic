<?php

namespace Famoser\Elliptic\Math\Primitives;

class ExtendedCoordinates
{
    public function __construct(public \GMP $X, public \GMP $Y, public \GMP $Z, public \GMP $T)
    {
    }

    public function equals(ExtendedCoordinates $other): bool
    {
        return gmp_cmp($this->X, $other->X) === 0 && gmp_cmp($this->Z, $other->Z) === 0;
    }
}
