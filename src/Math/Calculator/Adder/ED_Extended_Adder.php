<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Math\Primitives\ProjectiveCoordinates;

/**
 * Assumes Untwisted Edwards curve
 * Hence of the form ax^2 + y^2 = 1 + d*x^2*y^2
 *
 * Algorithms taken from RFC: https://datatracker.ietf.org/doc/html/rfc8032#section-5.2
 * RFC references https://eprint.iacr.org/2007/286.pdf as source
 * It is the best strongly unified variant according to: https://www.hyperelliptic.org/EFD/g1p/auto-edwards-projective.html#addition-add-2007-bl
 * Strongly unified is important as it supports points regardless of whether they are at 0, are the same or different.
 *
 * For our curves it holds that c = 1
 */
trait ED_Extended_Adder
{
    /**
     * Algorithm 5.2.4: Complete point addition (rfc8032)
     * Equivalent to algorithm at the end of page 9 in https://eprint.iacr.org/2007/286.pdf
     * Cost: 10M + 1S + 1*d + 6add (for M multiplication, S squaring, d multiplication by d, add additions)
     */
    public function add(ProjectiveCoordinates $a, ProjectiveCoordinates $b): ProjectiveCoordinates
    {
        $X1 = $a->X;
        $Y1 = $a->Y;
        $Z1 = $a->Z;
        $X2 = $b->X;
        $Y2 = $b->Y;
        $Z2 = $b->Z;

        $d = $this->curve->getB(); // we store d in B

        $A = $this->field->mul($Z1, $Z2);
        $B = $this->field->pow($A, 2);
        $C = $this->field->mul($X1, $X2);
        $D = $this->field->mul($Y1, $Y2);
        $E = $this->field->mul($d, $this->field->mul($C, $D));
        $F = $this->field->sub($B, $E);
        $G = $this->field->add($B, $E);
        $H1 = $this->field->add($X1, $Y1);
        $H2 = $this->field->add($X2, $Y2);
        $H = $this->field->mul($H1, $H2);
        $X31 = $this->field->sub($this->field->sub($H, $C), $D);
        $X3 = $this->field->mul($A, $this->field->mul($F, $X31));
        $Y31 = $this->field->sub($D, $C);
        $Y3 = $this->field->mul($A, $this->field->mul($G, $Y31));
        $Z3 = $this->field->mul($F, $G);

        return new ProjectiveCoordinates($X3, $Y3, $Z3);
    }

    /***
     * Algorithm 5.2.4: Point doubling (rfc8032)
     * Equivalent to https://www.hyperelliptic.org/EFD/g1p/auto-edwards-projective.html#doubling-dbl-2007-bl
     * Equivalent to https://eprint.iacr.org/2007/286.pdf first half of page 10 (doubling)
     * Cost: 3M + 4S + 5add + 1*2 (for M multiplication, S square, m2 multiplication by 2, add additions/subtractions)
     */
    public function double(ProjectiveCoordinates $a): ProjectiveCoordinates
    {
        $X1 = $a->X;
        $Y1 = $a->Y;
        $Z1 = $a->Z;

        $B1 = $this->field->add($X1, $Y1);
        $B = $this->field->pow($B1, 2);
        $C = $this->field->pow($X1, 2);
        $D = $this->field->pow($Y1, 2);
        $E = $this->field->add($C, $D);
        $H = $this->field->pow($Z1, 2);
        $J = $this->field->sub($E, $this->field->mul(gmp_init(2), $H));
        $X31 = $this->field->sub($B, $E);
        $X3 = $this->field->mul($X31, $J);
        $Y31 = $this->field->sub($C, $D);
        $Y3 = $this->field->mul($E, $Y31);
        $Z3 = $this->field->mul($E, $J);

        return new ProjectiveCoordinates($X3, $Y3, $Z3);
    }
}
