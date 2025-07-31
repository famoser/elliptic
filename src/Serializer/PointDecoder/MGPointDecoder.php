<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromCoordinatesTrait;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromXCoordinateTrait;

class MGPointDecoder extends AbstractPointDecoder
{
    use FromCoordinatesTrait;
    use FromXCoordinateTrait;

    public function __construct(private readonly Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this decoder
        $check = $curve->getType() === CurveType::Montgomery;
        if (!$check) {
            throw new \AssertionError('Cannot use this decoder with the chosen curve.');
        }
    }

    /**
     * calculate b * y^2
     */
    private function calculateLeftSide(Point $p): \GMP
    {
        return gmp_mul(
            $this->curve->getB(),
            gmp_pow($p->y, 2)
        );
    }

    /**
     * calculate x^3 + ax^2 + x
     */
    private function calculateRightSide(Point $p): \GMP
    {
        return $this->calculateRightSideInternal($p->x);
    }

    /**
     * calculate (x^3 + ax^2 + x) / b
     */
    private function calculateYSquare(\GMP $x): \GMP
    {
        $right = $this->calculateRightSideInternal($x);
        return gmp_mul(
            $right,
            /** @phpstan-ignore-next-line */
            gmp_invert($this->curve->getB(), $this->curve->getP())
        );
    }

    /**
     * calculate x^3 + ax^2 + x
     */
    private function calculateRightSideInternal(\GMP $x): \GMP
    {
        return gmp_add(
            gmp_add(
                gmp_powm($x, 3, $this->curve->getP()),
                gmp_mul(
                    $this->curve->getA(),
                    gmp_pow($x, 2)
                )
            ),
            $x
        );
    }
}
