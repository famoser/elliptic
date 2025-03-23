<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Primitives\Curve;

abstract class BaseCalculator
{
    private readonly int $curveNBitLength;

    public function __construct(private readonly Curve $curve)
    {
        $this->curveNBitLength = strlen(gmp_strval($this->curve->getN(), 2));
    }

    protected function getCurveNBitLength(): int
    {
        return $this->curveNBitLength;
    }

    public function getCurve(): Curve
    {
        return $this->curve;
    }
}
