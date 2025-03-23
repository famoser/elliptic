<?php

namespace Famoser\Elliptic\Math\Calculator\Primitives;

/**
 * Math inside a prime field; hence always (mod p)
 */
class PrimeField
{
    public function __construct(private readonly \GMP $p)
    {
    }

    public function add(\GMP $a, \GMP $b): \GMP
    {
        $r = gmp_add($a, $b);
        return gmp_mod($r, $this->p);
    }

    public function mul(\GMP $a, \GMP $b): \GMP
    {
        $r = gmp_mul($a, $b);
        return gmp_mod($r, $this->p);
    }

    public function sub(\GMP $a, \GMP $b): \GMP
    {
        $r = gmp_sub($a, $b);
        return gmp_mod($r, $this->p);
    }

    public function invert(\GMP $z)
    {
        return gmp_invert($z, $this->p);
    }
}
