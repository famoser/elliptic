<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Primitives\Point;

/**
 * Assumes Short Weierstrass curve with a=-3
 * Hence of the form y^2 = x^3 + ax + b for a = -3 mod p.
 *
 * Algorithms taken directly from original publication: https://eprint.iacr.org/2015/1060
 * Chosen as it is the best strongly unified variant from: https://www.hyperelliptic.org/EFD/g1p/auto-shortw-projective-3.html
 * Strongly unified is important as it supports points regardless of whether they are at 0, are the same or different.
 */
trait SW_ANeg3_Jacobi_Adder
{
    /**
     * Algorithm 4: Complete, projective point addition
     * Cost: 12M + 2mb + 29a (for M multiplication, mb multiplication by b, a additions)
     */
    public function add(JacobiPoint $a, JacobiPoint $b): JacobiPoint
    {
        $X1 = $a->X;
        $Y1 = $a->Y;
        $Z1 = $a->Z;
        $X2 = $b->X;
        $Y2 = $b->Y;
        $Z2 = $b->Z;

        $b = $this->curve->getB();

        $t0 = $this->field->mul($X1, $X2);
        $t1 = $this->field->mul($Y1, $Y2);
        $t2 = $this->field->mul($Z1, $Z2);
        $t3 = $this->field->add($X1, $Y1);
        $t4 = $this->field->add($X2, $Y2);
        $t3 = $this->field->mul($t3, $t4);
        $t4 = $this->field->add($t0, $t1);
        $t3 = $this->field->sub($t3, $t4);
        $t4 = $this->field->add($Y1, $Z1);
        $X3 = $this->field->add($Y2, $Z2);
        $t4 = $this->field->mul($t4, $X3);
        $X3 = $this->field->add($t1, $t2);
        $t4 = $this->field->sub($t4, $X3);
        $X3 = $this->field->add($X1, $Z1);
        $Y3 = $this->field->add($X2, $Z2);
        $X3 = $this->field->mul($X3, $Y3);
        $Y3 = $this->field->add($t0, $t2);
        $Y3 = $this->field->sub($X3, $Y3);
        $Z3 = $this->field->mul($b, $t2);
        $X3 = $this->field->sub($Y3, $Z3);
        $Z3 = $this->field->add($X3, $X3);
        $X3 = $this->field->add($X3, $Z3);
        $Z3 = $this->field->sub($t1, $X3);
        $X3 = $this->field->add($t1, $X3);
        $Y3 = $this->field->mul($b, $Y3);
        $t1 = $this->field->add($t2, $t2);
        $t2 = $this->field->add($t1, $t2);
        $Y3 = $this->field->sub($Y3, $t2);
        $Y3 = $this->field->sub($Y3, $t0);
        $t1 = $this->field->add($Y3, $Y3);
        $Y3 = $this->field->add($t1, $Y3);
        $t1 = $this->field->add($t0, $t0);
        $t0 = $this->field->add($t1, $t0);
        $t0 = $this->field->sub($t0, $t2);
        $t1 = $this->field->mul($t4, $Y3);
        $t2 = $this->field->mul($t0, $Y3);
        $Y3 = $this->field->mul($X3, $Z3);
        $Y3 = $this->field->add($Y3, $t2);
        $X3 = $this->field->mul($t3, $X3);
        $X3 = $this->field->sub($X3, $t1);
        $Z3 = $this->field->mul($t4, $Z3);
        $t1 = $this->field->mul($t3, $t0);
        $Z3 = $this->field->add($Z3, $t1);

        return new JacobiPoint($X3, $Y3, $Z3);
    }

    /**
     * Algorithm 6: Exception-free point doubling
     * Cost: 8M + 3S + 2mb + 21a (for M multiplication, S subtraction, mb multiplication by b, a additions)
     */
    public function double(JacobiPoint $a): JacobiPoint
    {
        $X = $a->X;
        $Y = $a->Y;
        $Z = $a->Z;

        $b = $this->curve->getB();

        $t0 = $this->field->mul($X, $X);
        $t1 = $this->field->mul($Y, $Y);
        $t2 = $this->field->mul($Z, $Z);
        $t3 = $this->field->mul($X, $Y);
        $t3 = $this->field->add($t3, $t3);
        $Z3 = $this->field->mul($X, $Z);
        $Z3 = $this->field->add($Z3, $Z3);
        $Y3 = $this->field->mul($b, $t2);
        $Y3 = $this->field->sub($Y3, $Z3);
        $X3 = $this->field->add($Y3, $Y3);
        $Y3 = $this->field->add($X3, $Y3);
        $X3 = $this->field->sub($t1, $Y3);
        $Y3 = $this->field->add($t1, $Y3);
        $Y3 = $this->field->mul($X3, $Y3);
        $X3 = $this->field->mul($X3, $t3);
        $t3 = $this->field->add($t2, $t2);
        $t2 = $this->field->add($t2, $t3);
        $Z3 = $this->field->mul($b, $Z3);
        $Z3 = $this->field->sub($Z3, $t2);
        $Z3 = $this->field->sub($Z3, $t0);
        $t3 = $this->field->add($Z3, $Z3);
        $Z3 = $this->field->add($Z3, $t3);
        $t3 = $this->field->add($t0, $t0);
        $t0 = $this->field->add($t3, $t0);
        $t0 = $this->field->sub($t0, $t2);
        $t0 = $this->field->mul($t0, $Z3);
        $Y3 = $this->field->add($Y3, $t0);
        $t0 = $this->field->mul($Y, $Z);
        $t0 = $this->field->add($t0, $t0);
        $Z3 = $this->field->mul($t0, $Z3);
        $X3 = $this->field->sub($X3, $Z3);
        $Z3 = $this->field->mul($t0, $t1);
        $Z3 = $this->field->add($Z3, $Z3);
        $Z3 = $this->field->add($Z3, $Z3);

        return new JacobiPoint($X3, $Y3, $Z3);
    }

    /**
     * Algorithm 5: Complete, mixed point addition
     * Cost: 11M + 2mb + 23a (for M multiplication, mb multiplication by b, a additions)
     */
    public function addAffine(JacobiPoint $a, Point $b): JacobiPoint
    {
        $X1 = $a->X;
        $Y1 = $a->Y;
        $Z1 = $a->Z;
        $X2 = $b->x;
        $Y2 = $b->y;

        $b = $this->curve->getB();

        $t0 = $this->field->mul($X1, $X2);
        $t1 = $this->field->mul($Y1, $Y2);
        $t3 = $this->field->add($X2, $Y2);
        $t4 = $this->field->add($X1, $Y1);
        $t3 = $this->field->mul($t3, $t4);
        $t4 = $this->field->add($t0, $t1);
        $t3 = $this->field->sub($t3, $t4);
        $t4 = $this->field->mul($Y2, $Z1);
        $t4 = $this->field->add($t4, $Y1);
        $Y3 = $this->field->mul($X2, $Z1);
        $Y3 = $this->field->add($Y3, $X1);
        $Z3 = $this->field->mul($b, $Z1);
        $X3 = $this->field->sub($Y3, $Z3);
        $Z3 = $this->field->add($X3, $X3);
        $X3 = $this->field->add($X3, $Z3);
        $Z3 = $this->field->sub($t1, $X3);
        $X3 = $this->field->add($t1, $X3);
        $Y3 = $this->field->mul($b, $Y3);
        $t1 = $this->field->add($Z1, $Z1);
        $t2 = $this->field->add($t1, $Z1);
        $Y3 = $this->field->sub($Y3, $t2);
        $Y3 = $this->field->sub($Y3, $t0);
        $t1 = $this->field->add($Y3, $Y3);
        $Y3 = $this->field->add($t1, $Y3);
        $t1 = $this->field->add($t0, $t0);
        $t0 = $this->field->add($t1, $t0);
        $t0 = $this->field->sub($t0, $t2);
        $t1 = $this->field->mul($t4, $Y3);
        $t2 = $this->field->mul($t0, $Y3);
        $Y3 = $this->field->mul($X3, $Z3);
        $Y3 = $this->field->add($Y3, $t2);
        $X3 = $this->field->mul($t3, $X3);
        $X3 = $this->field->sub($X3, $t1);
        $Z3 = $this->field->mul($t4, $Z3);
        $t1 = $this->field->mul($t3, $t0);
        $Z3 = $this->field->add($Z3, $t1);

        return new JacobiPoint($X3, $Y3, $Z3);
    }
}
