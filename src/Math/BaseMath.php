<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\Base\CalculatorInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * @template T
 */
abstract class BaseMath implements MathInterface
{
    /**
     * @param CalculatorInterface<T> $calculator
     */
    public function __construct(private readonly CalculatorInterface $calculator)
    {
    }

    public function getCurve(): Curve
    {
        return $this->calculator->getCurve();
    }

    public function mulG(\GMP $factor): Point
    {
        return $this->mul($this->calculator->getCurve()->getG(), $factor);
    }

    public function double(Point $a): Point
    {
        $nativeA = $this->calculator->affineToNative($a);

        $nativeResult = $this->calculator->double($nativeA);

        return $this->calculator->nativeToAffine($nativeResult);
    }

    public function add(Point $a, Point $b): Point
    {
        $nativeA = $this->calculator->affineToNative($a);
        $nativeB = $this->calculator->affineToNative($b);

        $nativeResult = $this->calculator->add($nativeA, $nativeB);

        return $this->calculator->nativeToAffine($nativeResult);
    }
}
