<?php

namespace Famoser\Elliptic\Primitives;

/**
 * In the finite field F_p,
 * an elliptic curve of the form y^2 = x^3 + ax + b is defined,
 * forming a group over addition.
 *
 * A base point G of order n and cofactor h is picked in this group.
 */
class Curve
{
    public function __construct(private readonly CurveType $type, private readonly \GMP $p, private readonly \GMP $a, private readonly \GMP $b, private readonly Point $G, private readonly \GMP $n, private readonly \GMP $h)
    {
    }

    public function getType(): CurveType
    {
        return $this->type;
    }

    public function getP(): \GMP
    {
        return $this->p;
    }

    public function getA(): \GMP
    {
        return $this->a;
    }

    public function getB(): \GMP
    {
        return $this->b;
    }

    public function getG(): Point
    {
        return $this->G;
    }

    public function getN(): \GMP
    {
        return $this->n;
    }

    public function getH(): \GMP
    {
        return $this->h;
    }
}
