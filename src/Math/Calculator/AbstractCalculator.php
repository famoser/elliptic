<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Primitives\PrimeField;
use Famoser\Elliptic\Primitives\Curve;

abstract class AbstractCalculator
{
    protected readonly PrimeField $field;
    protected readonly PrimeField $nField;

    public function __construct(protected readonly Curve $curve)
    {
        $this->field = new PrimeField($curve->getP());
        $this->nField = new PrimeField($curve->getN());
    }
}
