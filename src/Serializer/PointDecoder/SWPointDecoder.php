<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromCoordinatesTrait;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromXCoordinateTrait;

class SWPointDecoder extends AbstractPointDecoder
{
    use FromCoordinatesTrait;
    use FromXCoordinateTrait;

    public function __construct(private readonly Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this decoder
        $check = $curve->getType() === CurveType::ShortWeierstrass;
        if (!$check) {
            throw new \AssertionError('Cannot use this decoder with the chosen curve.');
        }
    }

    /**
     * calculate y^2
     */
    private function calculateLeftSide(Point $p): \GMP
    {
        return gmp_powm($p->y, 2, $this->curve->getP());
    }

    /**
     * calculate x^3 + ax + b
     */
    private function calculateRightSide(Point $p): \GMP
    {
        return $this->calculateYSquare($p->x);
    }

    /**
     * calculate x^3 + ax + b
     */
    private function calculateYSquare(\GMP $x): \GMP
    {
        return gmp_add(
            gmp_add(
                gmp_powm($x, gmp_init(3, 10), $this->curve->getP()),
                gmp_mul($this->curve->getA(), $x)
            ),
            $this->curve->getB()
        );
    }
}
