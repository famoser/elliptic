<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

class MGPointDecoder
{
    public function __construct(private readonly Curve $curve)
    {
        // check allowed to use this decoder
        $check = $curve->getType() === CurveType::Montgomery;
        if (!$check) {
            throw new \AssertionError('Cannot use this decoder with the chosen curve.');
        }
    }

    /**
     * @throws PointDecoderException
     */
    public function fromCoordinates(\GMP $x, \GMP $y): Point
    {
        $point = new Point($x, $y);

        if (!$this->fulfillsDefiningEquation($point)) {
            throw new PointDecoderException('Point not on curve.');
        }

        return $point;
    }

    /**
     * implements https://datatracker.ietf.org/doc/html/rfc8032#section-5.1.1
     * @throws PointDecoderException
     */
    public function fromXCoordinate(\GMP $x): Point
    {
        $p = $this->curve->getP();
        if (gmp_cmp(gmp_mod($p, 8), 5) !== 0) {
            throw new PointDecoderException('Point decoding for p mod 8 != 5 not implemented.');
        }

        $alpha = gmp_mod($this->calculateComparand($x), $p);

        /*
         * take the square root of alpha, while doing a (much cheaper) exponentiation
         *
         * observe that alpha^((p+3)/8) = y^((p+3)/4) = candidate
         * (p+3)/8 is an integer, as for our prime p it holds that p mod 8 = 5
         */
        $const = gmp_div(gmp_add($p, 3), 8);
        $candidate = gmp_powm($alpha, $const, $p);

        $candidateSquare = gmp_powm($candidate, 2, $p);
        if (gmp_cmp($candidateSquare, $alpha) === 0) {
            return new Point($x, $candidate);
        } else {
            $check = gmp_mod(gmp_add($candidateSquare, $alpha), $p);
            if (gmp_cmp($check, 0) === 0) {
                $const = gmp_div(gmp_sub($p, 1), 4);
                $correctionFactor = gmp_powm(2, $const, $p);
                $correctedCandidate = gmp_mod(gmp_mul($candidate, $correctionFactor), $p);

                return new Point($x, $correctedCandidate);
            }

            throw new PointDecoderException('No square root of alpha.');
        }
    }

    /**
     * montgomery defined by by^2 = x^3 + ax^2 + x
     */
    private function fulfillsDefiningEquation(Point $point): bool
    {
        $left = gmp_mul(
            $this->curve->getB(),
            gmp_pow($point->y, 2)
        );

        $right = $this->calculateComparand($point->x);

        $comparison = gmp_mod(
            gmp_sub($left, $right),
            $this->curve->getP()
        );

        return gmp_cmp($comparison, 0) == 0;
    }

    /**
     * calculate x^3 + ax^2 + x
     */
    private function calculateComparand(\GMP $x): \GMP
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
