<?php

namespace Famoser\Elliptic\Math\Calculator\Base;

use Famoser\Elliptic\Math\Calculator\Primitives\JacobiPoint;
use Famoser\Elliptic\Math\Calculator\Primitives\PrimeField;
use Famoser\Elliptic\Math\Utils\SwapperInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Jacobi coordinates (X,Y,Z) chosen such that affine coordinates (x=X/Z, y=Y/Z).
 *
 * @implements CalculatorInterface<JacobiPoint>
 */
abstract class AbstractJacobiCalculator extends AbstractCalculator
{
    public function __construct(Curve $curve, private readonly SwapperInterface $swapper, private readonly PrimeField $field)
    {
        parent::__construct($curve);
    }

    public function affineToNative(Point $point): JacobiPoint
    {
        // for Z = 1, it holds that X = x and Y = y
        return new JacobiPoint($point->x, $point->y, gmp_init(1));
    }

    public function nativeToAffine(mixed $nativePoint): Point
    {
        // to get x, need to calculate X/Z; same for y
        $zInverse = $this->field->invert($nativePoint->Z);
        $x = $this->field->mul($nativePoint->X, $zInverse);
        $y = $this->field->mul($nativePoint->Y, $zInverse);

        return new Point($x, $y);
    }

    public function getNativeInfinity(): JacobiPoint
    {
        return JacobiPoint::createInfinity();
    }

    public function conditionalSwap(mixed $a, mixed $b, int $swapBit): void
    {
        $this->swapper->conditionalSwap($a->X, $b->X, $swapBit, $this->field->getElementBitLength());
        $this->swapper->conditionalSwap($a->Y, $b->Y, $swapBit, $this->field->getElementBitLength());
        $this->swapper->conditionalSwap($a->Z, $b->Z, $swapBit, $this->field->getElementBitLength());
    }
}
