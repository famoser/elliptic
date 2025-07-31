<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromCoordinatesTrait;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromXCoordinateTrait;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromYCoordinateTrait;

class TwEDPointDecoder extends AbstractPointDecoder
{
    use FromCoordinatesTrait;
    use FromXCoordinateTrait;
    use FromYCoordinateTrait;

    public function __construct(private readonly Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this decoder
        $check = $curve->getType() === CurveType::TwistedEdwards;
        if (!$check) {
            throw new \AssertionError('Cannot use this decoder with the chosen curve.');
        }
    }

    /**
     * calculate ax² + y²
     */
    private function calculateLeftSide(\GMP $y, \GMP $x): \GMP
    {
        return gmp_add(
            gmp_mul(
                $this->curve->getA(),
                gmp_pow($x, 2)
            ),
            gmp_pow($y, 2)
        );
    }

    /**
     * calculate 1 + d*x²*y²
     */
    private function calculateRightSide(\GMP $x, \GMP $y): \GMP
    {
        return gmp_add(
            gmp_init(1),
            gmp_mul(
                $this->curve->getB(),
                gmp_mul(
                    gmp_powm($x, 2, $this->curve->getP()),
                    gmp_powm($y, 2, $this->curve->getP()),
                )
            )
        );
    }

    /**
     * calculate (1 - ax²) / (1 - dx²)
     */
    private function calculateYSquare(\GMP $x): \GMP
    {
        $xSquare = gmp_powm($x, 2, $this->curve->getP());
        $num = gmp_mod(gmp_sub(1, gmp_mul($this->curve->getA(), $xSquare)), $this->curve->getP());
        $den = gmp_mod(gmp_sub(1, gmp_mul($this->curve->getB(), $xSquare)), $this->curve->getP());

        return gmp_mul(
            $num,
            /** @phpstan-ignore-next-line */
            gmp_invert($den, $this->curve->getP())
        );
    }

    /**
     * calculate (1 - y²) / (a - dy²)
     */
    private function calculateXSquare(\GMP $y): \GMP
    {
        $ySquare = gmp_powm($y, 2, $this->curve->getP());
        $num = gmp_mod(gmp_sub(1, $ySquare), $this->curve->getP());
        $den = gmp_mod(gmp_sub($this->curve->getA(), gmp_mul($this->curve->getB(), $ySquare)), $this->curve->getP());

        return gmp_mul(
            $num,
            /** @phpstan-ignore-next-line */
            gmp_invert($den, $this->curve->getP())
        );
    }
}
