<?php

namespace Famoser\Elliptic\Math\Primitives;

/**
 * Math inside a prime field; hence always (mod p)
 */
class PrimeField
{
    private int $elementBitLength;

    public function __construct(private readonly \GMP $prime)
    {
        $this->elementBitLength = strlen(gmp_strval($prime, 2));
    }

    public function getElementBitLength(): int
    {
        return $this->elementBitLength;
    }

    public function add(\GMP $a, \GMP $b): \GMP
    {
        $r = gmp_add($a, $b);
        return gmp_mod($r, $this->prime);
    }

    public function mul(\GMP $a, \GMP $b): \GMP
    {
        $r = gmp_mul($a, $b);
        return gmp_mod($r, $this->prime);
    }

    public function sq(\GMP $a): \GMP
    {
        $r = gmp_mul($a, $a);
        return gmp_mod($r, $this->prime);
    }

    public function sub(\GMP $a, \GMP $b): \GMP
    {
        $r = gmp_sub($a, $b);
        return gmp_mod($r, $this->prime);
    }

    public function mod(\GMP $a): \GMP
    {
        return gmp_mod($a, $this->prime);
    }

    public function invert(\GMP $z): \GMP|false
    {
        return gmp_invert($z, $this->prime);
    }

    public function pow(\GMP $z, int $factor): \GMP
    {
        return gmp_powm($z, $factor, $this->prime);
    }
}
