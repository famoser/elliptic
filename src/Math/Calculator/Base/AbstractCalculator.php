<?php

namespace Famoser\Elliptic\Math\Calculator\Base;

use Famoser\Elliptic\Primitives\Curve;

/**
 * @template T
 * 
 * @implements CalculatorInterface<T>
 */
abstract class AbstractCalculator implements CalculatorInterface
{
    public function __construct(private readonly Curve $curve)
    {
    }

    public function getCurve(): Curve
    {
        return $this->curve;
    }
}
