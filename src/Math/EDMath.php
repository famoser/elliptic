<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\EDCalculator;
use Famoser\Elliptic\Math\Traits\NativeMathTrait;
use Famoser\Elliptic\Primitives\Curve;

/**
 * (Untwisted) Edwards math
 */
class EDMath extends AbstractMath implements MathInterface
{
    use NativeMathTrait;

    private readonly EDCalculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new EDCalculator($curve);
    }
}
