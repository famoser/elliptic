<?php

namespace Famoser\Elliptic\Math\Twister;

use Famoser\Elliptic\Math\Primitives\PrimeField;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Primitives\QuadraticTwist;

class QuadraticTwister
{
    private readonly PrimeField $field;

    public function __construct(private readonly Curve $curve, private readonly QuadraticTwist $twist)
    {
        $this->field = new PrimeField($curve->getP());
    }

    public function twistCurve(): Curve
    {
        $Z4 = $this->field->pow($this->twist->getZ(), 4);
        $Z6 = $this->field->pow($this->twist->getZ(), 6);

        $a = $this->field->mul($this->curve->getA(), $Z4);
        $b = $this->field->mul($this->curve->getB(), $Z6);
        $G = $this->twistPoint($this->curve->getG());

        return new Curve($this->curve->getType(), $this->curve->getP(), $a, $b, $G, $this->curve->getN(), $this->curve->getH());
    }

    public function twistPoint(Point $point): Point
    {
        $Z2 = $this->field->pow($this->twist->getZ(), 2);
        $Z3 = $this->field->pow($this->twist->getZ(), 3);

        $x = $this->field->mul($point->x, $Z2);
        $y = $this->field->mul($point->y, $Z3);

        return new Point($x, $y);
    }

    public function untwistPoint(Point $point): Point
    {
        $Z2 = $this->field->pow($this->twist->getZ(), 2);
        $Z3 = $this->field->pow($this->twist->getZ(), 3);

        $Z2inv = $this->field->invert($Z2) ?: gmp_init(0);
        $Z3inv = $this->field->invert($Z3) ?: gmp_init(0);

        $x = $this->field->mul($point->x, $Z2inv);
        $y = $this->field->mul($point->y, $Z3inv);

        return new Point($x, $y);
    }
}
