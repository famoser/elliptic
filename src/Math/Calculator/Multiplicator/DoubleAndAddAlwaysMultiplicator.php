<?php

namespace Famoser\Elliptic\Math\Calculator\Multiplicator;

/**
 * @template T
 */
trait DoubleAndAddAlwaysMultiplicator
{
    /**
     * @param T $point
     * @param \GMP $factor
     * @return T
     */
    public function mul(mixed $point, \GMP $factor): mixed
    {
        // reduce factor once to ensure it is within our curve N bit size (and reduce computational effort)
        $reducedFactor = $this->nField->mod($factor);

        // normalize to the element bit length to always execute the double-add loop a constant number of times
        $factorBits = gmp_strval($reducedFactor, 2);
        $normalizedFactorBits = str_pad($factorBits, $this->nField->getElementBitLength(), '0', STR_PAD_LEFT);

        /**
         * how this works:
         * first, observe r[0] is infinity and r[1] our "real" point.
         * r[0] and r[1] are swapped iff the corresponding bit in $factor is set to 1,
         * hence if $j = 1, then the "real" point is added, else the "real" point is doubled
         */
        /** @var T[] $r */
        $r = [$this->getInfinity(), clone $point];
        for ($i = 0; $i < $this->nField->getElementBitLength(); $i++) {
            $j = (int) $normalizedFactorBits[$i];

            $this->conditionalSwap($r[0], $r[1], $j ^ 1);

            $r[0] = $this->add($r[0], $r[1]);
            $r[1] = $this->double($r[1]);

            $this->conditionalSwap($r[0], $r[1], $j ^ 1);
        }

        return $r[0];
    }
}
