<?php

namespace Famoser\Elliptic\Math\Calculator\Base;

use Famoser\Elliptic\Math\Calculator\Primitives\PrimeField;
use Famoser\Elliptic\Math\Utils\SwapperInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * @extends AbstractCalculator<Point>
 * @implements CalculatorInterface<Point>
 */
abstract class AbstractPointCalculator extends AbstractCalculator implements CalculatorInterface
{
    public function __construct(Curve $curve, private readonly SwapperInterface $swapper, private readonly PrimeField $field)
    {
        parent::__construct($curve);
    }

    public function affineToNative(Point $point): Point
    {
        return clone $point;
    }

    public function nativeToAffine(mixed $nativePoint): Point
    {
        return clone $nativePoint;
    }

    public function getNativeInfinity(): Point
    {
        return Point::createInfinity();
    }

    public function conditionalSwap(mixed $a, mixed $b, int $swapBit): void
    {
        $this->swapper->conditionalSwap($a->x, $b->x, $swapBit, $this->field->getElementBitLength());
        $this->swapper->conditionalSwap($a->y, $b->y, $swapBit, $this->field->getElementBitLength());
    }
}
