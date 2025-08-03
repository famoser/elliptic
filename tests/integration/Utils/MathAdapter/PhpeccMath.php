<?php

namespace Famoser\Elliptic\Tests\Integration\Utils\MathAdapter;

use Famoser\Elliptic\Math\AbstractMath;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Mdanter\Ecc\Math\ConstantTimeMath;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Optimized\OptimizedCurveOpsInterface;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\PointInterface;

class PhpeccMath extends AbstractMath implements MathInterface
{
    private readonly GmpMathInterface $math;
    private readonly \GMP $order;
    public function __construct(Curve $curve, private readonly CurveFpInterface $curveFp, private readonly OptimizedCurveOpsInterface $curveOps)
    {
        parent::__construct($curve);

        $this->math = new ConstantTimeMath();

        $reflection = new \ReflectionClass($this->curveOps);
        $property = $reflection->getProperty('order');
        $this->order = $property->getValue($this->curveOps);
    }

    public function isInfinity(Point $point): bool
    {
        return gmp_cmp($point->x, 0) === 0;
    }

    public function getInfinity(): Point
    {
        return new Point(gmp_init(0), gmp_init(0));
    }

    public function double(Point $a): Point
    {
        $aNative = $this->convertToPointInterface($a);

        $resultNative = $this->curveOps->doublePoint($aNative);

        return $this->convertFromPointInterface($resultNative);
    }

    public function add(Point $a, Point $b): Point
    {
        $aNative = $this->convertToPointInterface($a);
        $bNative = $this->convertToPointInterface($b);

        $resultNative = $this->curveOps->addPoints($aNative, $bNative);

        return $this->convertFromPointInterface($resultNative);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $pointNative = $this->convertToPointInterface($point);

        $resultNative = $this->curveOps->scalarMult($factor, $pointNative);

        return $this->convertFromPointInterface($resultNative);
    }

    private function convertToPointInterface(Point $point): PointInterface
    {
        return new \Mdanter\Ecc\Primitives\Point($this->math, $this->curveFp, $point->x, $point->y, $this->order);
    }

    private function convertFromPointInterface(PointInterface $point): Point
    {
        return new Point($point->getX(), $point->getY());
    }
}
