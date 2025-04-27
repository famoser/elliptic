<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Twister\QuadraticTwister;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Primitives\QuadraticTwist;

class SW_QT_ANeg3_Math extends AbstractMath implements MathInterface
{
    private readonly QuadraticTwister $twister;
    private readonly SW_ANeg3_Math $math;

    public function __construct(Curve $curve, QuadraticTwist $twist)
    {
        parent::__construct($curve);

        $this->twister = new QuadraticTwister($curve, $twist);
        $twistedCurve = $this->twister->twistCurve();

        $this->math = new SW_ANeg3_Math($twistedCurve);
    }

    public function add(Point $a, Point $b): Point
    {
        $twistedA = $this->twister->twistPoint($a);
        $twistedB = $this->twister->twistPoint($b);

        $twistedResult = $this->math->add($twistedA, $twistedB);

        return $this->twister->untwistPoint($twistedResult);
    }

    public function double(Point $a): Point
    {
        $twistedA = $this->twister->twistPoint($a);

        $twistedResult = $this->math->double($twistedA);

        return $this->twister->untwistPoint($twistedResult);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $twisted = $this->twister->twistPoint($point);

        $twistedResult = $this->math->mul($twisted, $factor);

        return $this->twister->untwistPoint($twistedResult);
    }
}
