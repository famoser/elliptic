<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Primitives\Curve;

abstract class BaseCalculator
{
    public function __construct(private readonly Curve $curve)
    {
    }

    public function getCurve(): Curve
    {
        return $this->curve;
    }
}
