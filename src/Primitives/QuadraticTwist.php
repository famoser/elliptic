<?php

namespace Famoser\Elliptic\Primitives;

/**
 * Quadratic twist of an elliptic curve defined by: y^2 = x^3 + Z^4*A*x + Z^6*B mod p
 * With the GF(p)-isomorphism given by F(x,y) := (x*Z^2, y*Z^3)
 */
class QuadraticTwist
{
    public function __construct(private readonly \GMP $Z)
    {
    }

    public function getZ(): \GMP
    {
        return $this->Z;
    }
}
