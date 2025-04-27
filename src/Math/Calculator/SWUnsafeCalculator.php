<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\SWUnsafeAdder;
use Famoser\Elliptic\Math\Calculator\Coordinator\PointCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\DoubleAndAddAlwaysMultiplicator;
use Famoser\Elliptic\Math\Calculator\Swapper\PointSwapper;
use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * General-purpose calculator, but uses an unsafe adder.
 */
class SWUnsafeCalculator extends AbstractCalculator
{
    use PointCoordinator;
    use SWUnsafeAdder;
    use PointSwapper;
    /** @use DoubleAndAddAlwaysMultiplicator<Point> */
    use DoubleAndAddAlwaysMultiplicator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this calculator
        $check = $curve->getType() === CurveType::ShortWeierstrass;
        if (!$check) {
            throw new \AssertionError('Cannot use this calculator with the chosen curve.');
        }
    }
}
