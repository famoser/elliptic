<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\EDUnsafeCalculator;
use Famoser\Elliptic\Math\Calculator\TwEDUnsafeCalculator;
use Famoser\Elliptic\Math\Traits\MathTrait;
use Famoser\Elliptic\Primitives\Curve;

/**
 * Twisted Edwards math
 */
class TwEDUnsafeMath extends AbstractMath implements MathInterface
{
    use MathTrait;

    private readonly TwEDUnsafeCalculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new TwEDUnsafeCalculator($curve);
    }
}
