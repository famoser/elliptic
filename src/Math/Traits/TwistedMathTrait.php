<?php

namespace Famoser\Elliptic\Math\Traits;

use Famoser\Elliptic\Primitives\Point;

trait TwistedMathTrait
{
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
