<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\MGUnsafeCalculator;
use Famoser\Elliptic\Math\Traits\MathTrait;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Supports Montgomery curves by implementing their default calculation rules.
 *
 * This is in general unsafe, as not hardened against side-channels.
 */
class MGUnsafeMath extends AbstractMath implements MathInterface
{
    use MathTrait;

    private readonly MGUnsafeCalculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new MGUnsafeCalculator($curve);
    }
}
