<?php

namespace Famoser\Elliptic\Math\Primitives;

class JacobiPoint
{
    public function __construct(public \GMP $X, public \GMP $Y, public \GMP $Z)
    {
    }

    public static function createInfinity(): JacobiPoint
    {
        return new JacobiPoint(gmp_init(0), gmp_init(1), gmp_init(0));
    }

    public function isInfinity(): bool
    {
        return gmp_cmp($this->Z, 0) === 0;
    }

    public function equals(JacobiPoint $other): bool
    {
        return gmp_cmp($this->X, $other->X) === 0 && gmp_cmp($this->Y, $other->Y) === 0 && gmp_cmp($this->Z, $other->Z) === 0;
    }
}
