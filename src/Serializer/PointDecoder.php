<?php

namespace Famoser\Elliptic\Serializer;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

class PointDecoder
{
    public function __construct(private readonly Curve $curve)
    {
    }

    /**
     * @throws PointDecoderException
     */
    public function fromCoordinates(\GMP $x, \GMP $y): Point
    {
        $point = new Point($x, $y);
        if (!$this->fulfillsDefiningEquationOfCurve($point)) {
            throw new PointDecoderException('Point not on curve.');
        }

        return $point;
    }

    /**
     * implements https://www.secg.org/sec1-v2.pdf 2.3.4
     * @throws PointDecoderException
     */
    public function fromXCoordinate(\GMP $x, bool $isEvenY): Point
    {
        $p = $this->curve->getP();
        if (gmp_cmp(gmp_mod($p, 4), 3) !== 0) {
            throw new PointDecoderException('Point decoding for p mod 4 != 3 not implemented.');
        }

        $alpha = gmp_add(
            gmp_add(
                gmp_powm($x, gmp_init(3, 10), $p),
                gmp_mul($this->curve->getA(), $x)
            ),
            $this->curve->getB()
        );

        $jacobiSymbol = gmp_jacobi($alpha, $p);
        if ($jacobiSymbol !== 1) {
            throw new PointDecoderException('No square root of alpha.');
        }

        /*
         * take the square root of alpha, while doing a (much cheaper) exponentiation
         *
         * observe that alpha^((p+1)/4) = y^((p+1)/2) = y^((p-1)/2) * y = y
         * (p+1)/4 is an integer, as for our prime p it holds that p mod 4 = 3
         * alpha = y^2 by the jacobi symbol check above that asserts y is a quadratic residue
         * y^((p-1)/2) = 1 by Euler's Criterion applies to the quadratic residue y
         */
        $const = gmp_div(gmp_add($p, 1), 4);
        $beta = gmp_powm($alpha, $const, $p);

        $yp = $isEvenY ? gmp_init(0) : gmp_init(1);
        if (gmp_cmp(gmp_mod($beta, 2), $yp) === 0) {
            return new Point($x, $beta);
        } else {
            return new Point($x, gmp_sub($p, $beta));
        }
    }

    /**
     * check fulfills defining equation of the curve
     * @throws PointDecoderException
     */
    private function fulfillsDefiningEquationOfCurve(Point $point): bool
    {
        return match ($this->curve->getType()) {
            CurveType::ShortWeierstrass => $this->fulfillsShortWeierstrassDefiningEquation($point),
            CurveType::Montgomery => throw new PointDecoderException('Montgomery not yet supported.'),
        };
    }

    /**
     * short weierstrass defined as y^2 = x^3 + ax + b
     */
    private function fulfillsShortWeierstrassDefiningEquation(Point $point): bool
    {
        $left = gmp_pow($point->y, 2);
        $right = gmp_add(
            gmp_add(
                gmp_pow($point->x, 3),
                gmp_mul($this->curve->getA(), $point->x)
            ),
            $this->curve->getB()
        );

        $comparison = gmp_mod(
            gmp_sub($left, $right),
            $this->curve->getP()
        );

        return gmp_cmp($comparison, 0) == 0;
    }
}
