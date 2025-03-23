<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Primitives\PrimeField;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

class UnsafeMath implements MathInterface
{
    private readonly int $curveNBitLength;
    private readonly PrimeField $field;

    public function __construct(private readonly Curve $curve)
    {
        $this->curveNBitLength = strlen(gmp_strval($this->curve->getN(), 2));
        $this->field = new PrimeField($curve->getP());
    }

    protected function getCurveNBitLength(): int
    {
        return $this->curveNBitLength;
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

        // rule 5 (note that a / b = a * b^-1)
        $lambda = $this->field->mul(
            gmp_add(
                gmp_mul(
                    gmp_init(3),
                    gmp_pow($a->x, 2)
                ),
                $this->curve->getA()
            ),
            $this->field->invert(
                gmp_mul(
                    gmp_init(2),
                    $a->y
                )
            )
        );

        $x = $this->field->sub(
            gmp_pow($lambda, 2),
            gmp_mul(gmp_init(2), $a->x)
        );

        $y = $this->field->sub(
            gmp_mul(
                $lambda,
                gmp_sub($a->x, $x)),
            $a->y
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

        // rule 4 (note that a / b = a * b^-1)
        $lambda = $this->field->mul(
            gmp_sub($b->y, $a->y),
            $this->field->invert(gmp_sub($b->x, $a->x))
        );

        $x = $this->field->sub(
            gmp_sub(
                gmp_pow($lambda, 2),
                $a->x),
            $b->x
        );

        $y = $this->field->sub(
            gmp_mul(
                $lambda,
                gmp_sub($a->x, $x)),
            $a->y
        );

        return new Point($x, $y);
    }

    public function mulG(\GMP $factor): Point
    {
        return $this->mul($this->curve->getG(), $factor);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        // reduce factor once to ensure it is within our curve N bit size (and reduce computational effort)
        $reducedFactor = gmp_mod($factor, $this->curve->getN());

        // normalize to curve N bit length to always execute the double-add loop a constant number of times
        $factorBits = gmp_strval($reducedFactor, 2);
        $normalizedFactorBits = str_pad($factorBits, $this->curveNBitLength, '0', STR_PAD_LEFT);

        /**
         * how this works:
         * first, observe r[0] is infinity at (0,0), and r[1] our "real" point.
         * r[0] and r[1] are swapped iff the corresponding bit in $factor is set to 1,
         * hence if $j = 1, then the "real" point is added, else the "real" point is doubled
         */
        /** @var Point[] $r */
        $r = [Point::createInfinity(), clone $point];
        for ($i = 0; $i < $this->curveNBitLength; $i++) {
            $j = $normalizedFactorBits[$i];

            $this->conditionalSwap($r[0], $r[1], $j ^ 1);

            $r[0] = $this->add($r[0], $r[1]);
            $r[1] = $this->double($r[1]);

            $this->conditionalSwap($r[0], $r[1], $j ^ 1);
        }

        return $r[0];
    }

    protected function conditionalSwap(Point $a, Point $b, int $swapBit): void
    {
        $this->scalarConditionalSwap($a->x, $b->x, $swapBit);
        $this->scalarConditionalSwap($a->y, $b->y, $swapBit);
    }

    protected function scalarConditionalSwap(\GMP &$a, \GMP &$b, int $swapBit): void
    {
        // create a mask (note how it inverts the maskbit)
        $mask = gmp_init(str_repeat((string)(1 - $swapBit), $this->curveNBitLength), 2);

        // if mask is 1, tempA = a, else temp = 0
        $tempA = gmp_and($a, $mask);
        $tempB = gmp_and($b, $mask);

        $a = gmp_xor($tempB, gmp_xor($a, $b)); // if mask is 1, then b XOR a XOR b = a, else 0 XOR a XOR b = a XOR b
        $b = gmp_xor($tempA, gmp_xor($a, $b)); // if mask is 1, then a XOR a XOR b = b, else 0 XOR a XOR b XOR b = a
        $a = gmp_xor($tempB, gmp_xor($a, $b)); // if mask is 1, then b XOR a XOR b = a, else 0 XOR a XOR b XOR a = b

        // hence if mask is 1 (= inverse of $swapBit), then no swap, else swap
    }
}
