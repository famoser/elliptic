<?php

namespace Famoser\Elliptic\Serializer\PointDecoder\Traits;

use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;

trait FromYCoordinateTrait
{
    use SimpleSquareRootTrait;

    /**
     * @throws PointDecoderException
     */
    public function fromYCoordinate(\GMP $y, ?bool $isEvenX = null): Point
    {
        $p = $this->curve->getP();

        $xx = gmp_mod($this->calculateXSquare($y), $p);
        $x = $this->simpleSquareRoot($xx, $isEvenX);

        return new Point($x, $y);
    }
}
