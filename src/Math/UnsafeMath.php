<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\Primitives\Curve;
use Mdanter\Ecc\Primitives\Point;

class UnsafeMath implements MathInterface
{
    private readonly int $curveNBitLength;

    public function __construct(private readonly Curve $curve)
    {
        $this->curveNBitLength = strlen(gmp_strval($this->curve->getN(), 2));
    }

    public function getCurve(): Curve
    {
        return $this->curve;
    }

    /**
     * rules from https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
     */
    public function double(Point $a): Point
    {
        if (gmp_cmp($a->y, 0) === 0) {
            return Point::createInfinity();
        }

        // rule 4
        $lambda = $this->scalarModDiv(
            gmp_add(
                gmp_mul(
                    gmp_init(3),
                    gmp_pow($a->x, 2)
                ),
                $this->curve->getA()
            ),
            gmp_mul(
                gmp_init(2),
                $a->y
            )
        );

        $x = gmp_mod(
            gmp_sub(
                gmp_pow($lambda, 2),
                gmp_mul(gmp_init(2), $a->x)
            ),
            $this->curve->getP()
        );

        $y = gmp_mod(
            gmp_sub(
                gmp_mul(
                    $lambda,
                    gmp_sub($a->x, $x)),
                $a->y
            ),
            $this->curve->getP()
        );

        return new Point($x, $y);
    }

    /**
     * rules from https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
     */
    public function add(Point $a, Point $b): Point
    {
        // rule 1 & 2
        if ($a->isInfinity()) {
            return clone $b;
        } else if ($b->isInfinity()) {
            return clone $a;
        }

        if (gmp_cmp($a->x, $b->x) === 0) {
            // rule 3
            if (gmp_cmp($b->y, $a->y) !== 0) {
                return Point::createInfinity();
            }

            // rule 5
            return $this->double($a);
        }

        // rule 4
        $lambda = $this->scalarModDiv(
            gmp_sub($b->y, $a->y),
            gmp_sub($b->x, $a->x)
        );

        $x = gmp_mod(
            gmp_sub(
                gmp_sub(
                    gmp_pow($lambda, 2),
                    $a->x),
                $b->x
            ),
            $this->curve->getP()
        );

        $y = gmp_mod(
            gmp_sub(
                gmp_mul(
                    $lambda,
                    gmp_sub($a->x, $x)),
                $a->y
            ),
            $this->curve->getP()
        );

        return new Point($x, $y);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $reducedFactor = gmp_mod($factor, $this->curve->getN());

        /** @var Point[] $r */
        $r = [Point::createInfinity(), clone $point];
        $factorBits = gmp_strval($reducedFactor, 2);
        $normalizedFactorBits = str_pad($factorBits, $this->curveNBitLength, '0', STR_PAD_LEFT);

        for ($i = 0; $i < $this->curveNBitLength; $i++) {
            $j = $normalizedFactorBits[$i];

            $this->conditionalSwap($r[0], $r[1], $j ^ 1);

            $r[0] = $this->add($r[0], $r[1]);
            $r[1] = $this->double($r[1]);

            $this->conditionalSwap($r[0], $r[1], $j ^ 1);
        }

        return $r[0];
    }

    private function scalarModDiv(\GMP $a, \GMP $d)
    {
        /**
         * it holds that a * d^-1 (mod p) ≡ a/d (mod p)
         *
         * proof:
         *  d * d^-1 ≡ 1 (mod p)               by modular inverse (that always exist in prime group p)
         *  a * d * d^-1 ≡ a (mod p)           by multiplying a on both sides
         *  a * d^-1 * d ≡ a (mod p)           by commutativity of multiplication
         */
        $inversion = gmp_invert($d, $this->curve->getP());
        return gmp_mul($a, $inversion);
    }

    private function conditionalSwap(Point $a, Point $b, int $swapBit): void
    {
        $this->scalarConditionalSwap($a->x, $b->x, $swapBit);
        $this->scalarConditionalSwap($a->y, $b->y, $swapBit);
    }

    private function scalarConditionalSwap(\GMP &$a, \GMP &$b, int $swapBit): void
    {
        // create a mask (note how it inverts the maskbit)
        $mask = gmp_init(str_repeat((string) (1 - $swapBit), $this->curveNBitLength), 2);

        // if mask is 1, tempA = a, else temp = 0
        $tempA = gmp_and($a, $mask);
        $tempB = gmp_and($b, $mask);

        $a = gmp_xor($tempB, gmp_xor($a, $b)); // if mask is 1, then b XOR a XOR b = a, else 0 XOR a XOR b = a XOR b
        $b = gmp_xor($tempA, gmp_xor($a, $b)); // if mask is 1, then a XOR a XOR b = b, else 0 XOR a XOR b XOR b = a
        $a = gmp_xor($tempB, gmp_xor($a, $b)); // if mask is 1, then b XOR a XOR b = a, else 0 XOR a XOR b XOR a = b

        // hence if mask is 1, then no swap, else swap
    }
}
