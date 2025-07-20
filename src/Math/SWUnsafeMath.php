<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\SWUnsafeCalculator;
use Famoser\Elliptic\Math\Traits\MathTrait;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Supports all Short Weierstrass curves by implementing their default calculation rules.
 *
 * This is in general unsafe, as not hardened against side-channels.
 */
class SWUnsafeMath extends AbstractMath implements MathInterface
{
    use MathTrait;

    private readonly SWUnsafeCalculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new SWUnsafeCalculator($curve);
    }
}
