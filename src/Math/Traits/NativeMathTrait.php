<?php

namespace Famoser\Elliptic\Math\Traits;

use Famoser\Elliptic\Primitives\Point;

trait NativeMathTrait
{
    public function isInfinity(Point $point): bool
    {
        $native = $this->calculator->affineToNative($point);

        return $this->calculator->isInfinity($native);
    }

    public function getInfinity(): Point
    {
        $native = $this->calculator->getInfinity();

        return $this->calculator->nativeToAffine($native);
    }

    public function add(Point $a, Point $b): Point
    {
        $nativeA = $this->calculator->affineToNative($a);
        $nativeB = $this->calculator->affineToNative($b);

        $nativeResult = $this->calculator->add($nativeA, $nativeB);

        return $this->calculator->nativeToAffine($nativeResult);
    }

    public function double(Point $a): Point
    {
        $nativeA = $this->calculator->affineToNative($a);

        $nativeResult = $this->calculator->double($nativeA);

        return $this->calculator->nativeToAffine($nativeResult);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $native = $this->calculator->affineToNative($point);

        $nativeResult = $this->calculator->mul($native, $factor);

        return $this->calculator->nativeToAffine($nativeResult);
    }
}
