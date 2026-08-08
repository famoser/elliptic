<?php

namespace Famoser\Elliptic\Validator;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;

class CofactorValidator
{
    public function __construct(private readonly MathInterface $math)
    {
    }

    public function isValidPoint(Point $point): bool
    {
        return !$this->hasSmallOrder($point) && $this->isInBasepointTorsion($point);
    }

    public function hasSmallOrder(Point $point): bool
    {
        $res = $this->math->mulH($point);

        return $res->equals($this->math->getInfinity());
    }

    public function isInBasepointTorsion(Point $point): bool
    {
        $res = $this->math->mul($point, $this->math->getCurve()->getN());

        return $res->equals($this->math->getInfinity());
    }
}
