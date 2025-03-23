<?php /** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Math\Primitives\PrimeField;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * Assumes Short Weierstrass curve with a=-3
 * Hence of the form y^2 = x^3 + ax + b for a = -3 mod p
 * Jacobi coordinates (X,Y,Z) chosen such that affine coordinates (x=X/Z, y=Y/Z)
 *
 * Best strongly unified variant from: https://www.hyperelliptic.org/EFD/g1p/auto-shortw-projective-3.html
 * Strongly unified is important as it supports points regardless of whether they are at 0, are the same or different.
 *
 * Original publication: https://eprint.iacr.org/2015/1060
 */
class UnsafeShortWeierstrassANeg3Math extends UnsafeMath implements MathInterface
{
    private readonly PrimeField $field;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        assert(gmp_cmp($curve->getA(), gmp_sub($curve->getP(), -3)) === 0);
        assert($curve->getType() === CurveType::ShortWeierstrass);

        $this->field = new PrimeField($curve->getP());
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        // reduce factor once to ensure it is within our curve N bit size (and reduce computational effort)
        $reducedFactor = gmp_mod($factor, $this->getCurve()->getN());

        // normalize to curve N bit length to always execute the double-add loop a constant number of times
        $factorBits = gmp_strval($reducedFactor, 2);
        $normalizedFactorBits = str_pad($factorBits, $this->getCurveNBitLength(), '0', STR_PAD_LEFT);

        /**
         * how this works:
         * first, observe r[0] is infinity and r[1] our "real" point.
         * r[0] and r[1] are swapped iff the corresponding bit in $factor is set to 1,
         * hence if $j = 1, then the "real" point is added, else the "real" point is doubled
         */
        /** @var JacobiPoint[] $r */
        $r = [JacobiPoint::createInfinity(), $this->affineToJacobi($point)];
        for ($i = 0; $i < $this->getCurveNBitLength(); $i++) {
            $j = $normalizedFactorBits[$i];

            $this->conditionalSwapJacobi($r[0], $r[1], $j ^ 1);

            $r[0] = $this->addJacobi($r[0], $r[1]);
            $r[1] = $this->doubleJacobi($r[1]);

            $this->conditionalSwapJacobi($r[0], $r[1], $j ^ 1);
        }

        return $this->jacobiToAffine($r[0]);
    }

    public function mulG(\GMP $factor): Point
    {
        /**
         * Optimization potential here:
         * - For window (e.g. of size 4 bits), precompute G into table t. Then, Q <- 2Q; Q <- Q + t[window]
         * - Generalize above method, precompute full table (to also avoid doubling)
         *
         * But needs to be measured whether actually some advantage, as:
         * - Table needs to be fully traversed, else private key imprinted in cache
         * - Especially costly as above implied swapping two values (x,y) repeatedly
         * - Maybe faster by encoding table as string, and only generating chosen gmp afterwards?
         */
        return $this->mul($this->getCurve()->getG(), $factor);
    }

    private function conditionalSwapJacobi(JacobiPoint $a, JacobiPoint $b, int $swapBit): void
    {
        $this->scalarConditionalSwap($a->X, $b->X, $swapBit);
        $this->scalarConditionalSwap($a->Y, $b->Y, $swapBit);
        $this->scalarConditionalSwap($a->Z, $b->Z, $swapBit);
    }

    private function affineToJacobi(Point $point): JacobiPoint
    {
        // for Z = 1, it holds that X = x and Y = y
        return new JacobiPoint($point->x, $point->y, gmp_init(1));
    }

    private function jacobiToAffine(JacobiPoint $jacobiPoint): Point
    {
        // to get x, need to calculate X/Z; same for y
        $zInverse = $this->field->invert($jacobiPoint->Z);
        $x = $this->field->mul($jacobiPoint->X, $zInverse);
        $y = $this->field->mul($jacobiPoint->Y, $zInverse);

        return new Point($x, $y);
    }

    /**
     * Algorithm 4: Complete, projective point addition
     * Cost: 12M + 2mb + 29a (for M multiplication, mb multiplication by b, a additions)
     */
    private function addJacobi(JacobiPoint $a, JacobiPoint $b): JacobiPoint
    {
        $X1 = $a->X;
        $Y1 = $a->Y;
        $Z1 = $a->Z;
        $X2 = $b->X;
        $Y2 = $b->Y;
        $Z2 = $b->Z;

        $b = $this->getCurve()->getB();

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
     * Algorithm 5: Complete, mixed point addition
     * Cost: 11M + 2mb + 23a (for M multiplication, mb multiplication by b, a additions)
     */
    private function addJacobiAffine(JacobiPoint $a, Point $b): JacobiPoint
    {
        $X1 = $a->X;
        $Y1 = $a->Y;
        $Z1 = $a->Z;
        $X2 = $b->x;
        $Y2 = $b->y;

        $b = $this->getCurve()->getB();

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


    /**
     * Algorithm 6: Exception-free point doubling
     * Cost: 8M + 3S + 2mb + 21a (for M multiplication, S subtraction, mb multiplication by b, a additions)
     */
    private function doubleJacobi(JacobiPoint $a): JacobiPoint
    {
        $X = $a->X;
        $Y = $a->Y;
        $Z = $a->Z;

        $b = $this->getCurve()->getB();

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
}
