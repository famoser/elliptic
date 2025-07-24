<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\EDCalculator;
use Famoser\Elliptic\Math\Calculator\EDUnsafeCalculator;
use Famoser\Elliptic\Math\Traits\MathTrait;
use Famoser\Elliptic\Math\Traits\NativeMathTrait;
use Famoser\Elliptic\Primitives\Curve;

/**
 * (Untwisted) Edwards math
 */
class EDUnsafeMath extends AbstractMath implements MathInterface
{
    use MathTrait;

    private readonly EDUnsafeCalculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new EDUnsafeCalculator($curve);
    }
}
