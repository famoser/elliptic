<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Primitives\PrimeField;
use Famoser\Elliptic\Primitives\Curve;

abstract class AbstractCalculator
{
    protected readonly PrimeField $field;
    protected readonly PrimeField $nhField;

    public function __construct(protected readonly Curve $curve)
    {
        $this->field = new PrimeField($curve->getP());
        $this->nhField = new PrimeField(gmp_mul($curve->getN(), $curve->getH()));
    }
}
