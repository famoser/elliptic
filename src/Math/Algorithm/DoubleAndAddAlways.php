<?php

namespace Famoser\Elliptic\Math\Algorithm;

use Famoser\Elliptic\Math\Calculator\CalculatorInterface;
use Famoser\Elliptic\Math\Calculator\Primitives\JacobiPoint;
use Famoser\Elliptic\Math\Calculator\Primitives\PrimeField;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * @template T
 */
class DoubleAndAddAlways
{
    private readonly Curve $curve;
    private readonly PrimeField $field;

    /**
     * @param CalculatorInterface<T> $calculator
     */
    public function __construct(private readonly CalculatorInterface $calculator)
    {
        $this->curve = $this->calculator->getCurve();
        $this->field = new PrimeField($this->curve->getN());
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        // reduce factor once to ensure it is within our curve N bit size (and reduce computational effort)
        $reducedFactor = $this->field->mod($factor);

        // normalize to the element bit length to always execute the double-add loop a constant number of times
        $factorBits = gmp_strval($reducedFactor, 2);
        $normalizedFactorBits = str_pad($factorBits, $this->field->getElementBitLength(), '0', STR_PAD_LEFT);

        /**
         * how this works:
         * first, observe r[0] is infinity and r[1] our "real" point.
         * r[0] and r[1] are swapped iff the corresponding bit in $factor is set to 1,
         * hence if $j = 1, then the "real" point is added, else the "real" point is doubled
         */
        /** @var T[] $r */
        $r = [$this->calculator->getNativeInfinity(), $this->calculator->affineToNative($point)];
        for ($i = 0; $i < $this->field->getElementBitLength(); $i++) {
            $j = $normalizedFactorBits[$i];

            $this->calculator->conditionalSwap($r[0], $r[1], $j ^ 1);

            $r[0] = $this->calculator->add($r[0], $r[1]);
            $r[1] = $this->calculator->double($r[1]);

            $this->calculator->conditionalSwap($r[0], $r[1], $j ^ 1);
        }

        return $this->calculator->nativeToAffine($r[0]);
    }
}
