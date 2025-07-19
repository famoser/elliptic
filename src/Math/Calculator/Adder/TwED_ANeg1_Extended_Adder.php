<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Math\Primitives\ExtendedCoordinates;
use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Primitives\Point;

/**
 * Assumes Twisted Edwards curve with a=-1
 * Hence of the form ax^2 + y^2 = 1 + d*x^2*y^2 for a = -1 mod p.
 *
 * Algorithms taken from RFC: https://datatracker.ietf.org/doc/html/rfc8032#section-5.1
 * RFC references https://eprint.iacr.org/2008/522.pdf as source
 * It is the best strongly unified variant according to: https://www.hyperelliptic.org/EFD/g1p/auto-twisted-extended-1.html#addition-add-2008-hwcd-3
 * Strongly unified is important as it supports points regardless of whether they are at 0, are the same or different.
 */
trait TwED_ANeg1_Extended_Adder
{
    /**
     * Algorithm 5.1.4: Complete point addition (rfc8032)
     * Equivalent to algorithm at the end of section 3.1 in https://eprint.iacr.org/2008/522.pdf
     * Cost: 8M + 1mk + 1m2 + 8a (for M multiplication, mb multiplication by k, m2 multiplication by 2, a additions)
     */
    public function add(ExtendedCoordinates $a, ExtendedCoordinates $b): ExtendedCoordinates
    {
        $X1 = $a->X;
        $Y1 = $a->Y;
        $Z1 = $a->Z;
        $T1 = $a->T;
        $X2 = $b->X;
        $Y2 = $b->Y;
        $Z2 = $b->Z;
        $T2 = $b->T;

        $d = $this->curve->getB(); // we use B to refer to d

        $A1 = $this->field->sub($Y1, $X1);
        $A2 = $this->field->sub($Y2, $X2);
        $A = $this->field->mul($A1, $A2);
        $B1 = $this->field->add($Y1, $X1);
        $B2 = $this->field->add($Y2, $X2);
        $B = $this->field->mul($B1, $B2);
        $k = $this->field->mul(gmp_init(2), $d); // TODO: Precompute
        $C1 = $this->field->mul($T1, $k);
        $C = $this->field->mul($C1, $T2);
        $D1 = $this->field->mul($Z1, gmp_init(2));
        $D = $this->field->mul($D1, $Z2);
        $E = $this->field->sub($B, $A);
        $F = $this->field->sub($D, $C);
        $G = $this->field->add($D, $C);
        $H = $this->field->add($B, $A);
        $X3 = $this->field->mul($E, $F);
        $Y3 = $this->field->mul($G, $H);
        $T3 = $this->field->mul($E, $H);
        $Z3 = $this->field->mul($F, $G);

        return new ExtendedCoordinates($X3, $Y3, $Z3, $T3);
    }

    /***
     * Algorithm 5.1.4: Point doubling (rfc8032)
     * Equivalent to https://www.hyperelliptic.org/EFD/g1p/auto-twisted-extended-1.html#doubling-dbl-2008-hwcd
     * Equivalent to http://eprint.iacr.org/2008/522 section 3.3 (dedicated doubling)
     * Cost: 4M + 4S + 1m2 + 5a (for M multiplication, S square, m2 multiplication by 2, a additions/subtractions)
     */
    public function double(ExtendedCoordinates $a): ExtendedCoordinates
    {
        $X1 = $a->X;
        $Y1 = $a->Y;
        $Z1 = $a->Z;

        $A = $this->field->pow($X1, 2);
        $B = $this->field->pow($Y1, 2);
        $C1 = $this->field->pow($Z1, 2);
        $C = $this->field->mul(gmp_init(2), $C1);
        $H = $this->field->add($A, $B);
        $E1 = $this->field->add($X1, $Y1);
        $E2 = $this->field->pow($E1, 2);
        $E = $this->field->sub($H, $E2);
        $G = $this->field->sub($A, $B);
        $F = $this->field->add($C, $G);
        $X3 = $this->field->mul($E, $F);
        $Y3 = $this->field->mul($G, $H);
        $T3 = $this->field->mul($E, $H);
        $Z3 = $this->field->mul($F, $G);

        return new ExtendedCoordinates($X3, $Y3, $Z3, $T3);
    }

    /**
     * Equivalence RFC / bernstein doubling:
     *
     * Bernstein:
     * A = X1^2
     * B = Y1^2
     * C = 2*Z1^2
     * D = a*A = -A
     * E = (X1+Y1)^2-A-B
     * G = D+B
     * F = G-C
     * H = D-B
     * X3 = E*F = ((X1+Y1)^2-A-B) * (G-C) = ((X1+Y1)^2-A-B) * ((D+B)-C) = ((X1+Y1)^2-A-B) * ((-A+B)-C) = ((X1+Y1)^2-A-B) * (-A+B-C) = -> equal, use -1 * -1 to transform
     * Y3 = G*H = (D+B)*(D-B) = (-A+B)*(-A-B)
     * T3 = E*H = ((X1+Y1)^2-A-B) * (D-B) = ((X1+Y1)^2-A-B) * (-A-B)
     * Z3 = F*G = (G-C)*(D+B) = ((D+B)-C) * (-A+B) = ((-A+B)-C) * (-A+B)
     *
     * RFC:
     * A = X1^2
     * B = Y1^2
     * C = 2*Z1^2
     * H = A+B
     * E = H-(X1+Y1)^2
     * G = A-B
     * F = C+G
     * X3 = E*F = (H-(X1+Y1)^2) * (C+G) = ((A+B)-(X1+Y1)^2) * (C+(A-B)) = (A+B-(X1+Y1)^2) * (C+A-B)
     * Y3 = G*H = (A-B)*(A+B)
     * T3 = E*H = (H-(X1+Y1)^2) * (A+B) = ((A+B)-(X1+Y1)^2) * (A+B)
     * Z3 = F*G = (C+G) * (A-B) = (C+A-B) * (A-B)
     *
     * Transform from RFC to bernstein by multiplying the two brackets by (-1) (note that -1*-1 = 1, hence can apply this transformation)
     */
}
