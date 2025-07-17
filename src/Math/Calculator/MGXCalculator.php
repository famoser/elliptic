<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Swapper\ScalarSwapper;

/**
 * implements https://datatracker.ietf.org/doc/html/rfc7748#section-5
 */
class MGXCalculator extends AbstractCalculator
{
    use ScalarSwapper;

    public function mul(\GMP $u, \GMP $factor): \GMP
    {
        // normalize to the element bit length to always execute the double-add loop a constant number of times
        $factorBits = gmp_strval($factor, 2);
        $normalizedFactorBits = str_pad($factorBits, $this->field->getElementBitLength(), '0', STR_PAD_LEFT);

        // precompute constants
        $a24 = gmp_div(gmp_sub($this->curve->getA(), 2), 4);
        $p2 = gmp_sub($this->curve->getP(), 2);

        $x1 = $u;
        $x2 = gmp_init(1);
        $z2 = gmp_init(0);
        $x3 = $u;
        $z3 = gmp_init(1);
        $swap = 0;
        for ($i = 0; $i < $this->field->getElementBitLength(); $i++) {
            $swap ^= (int)$normalizedFactorBits[$i];
            $this->conditionalSwapScalar($x2, $x3, $swap, $this->field->getElementBitLength());
            $this->conditionalSwapScalar($z2, $z3, $swap, $this->field->getElementBitLength());
            $swap = (int)$normalizedFactorBits[$i];

            $A = $this->field->add($x2, $z2);
            $AA = $this->field->pow($A, 2);
            $B = $this->field->sub($x2, $z2);
            $BB = $this->field->pow($B, 2);
            $E = $this->field->sub($AA, $BB);
            $C = $this->field->add($x3, $z3);
            $D = $this->field->sub($x3, $z3);
            $DA = $this->field->mul($D, $A);
            $CB = $this->field->mul($C, $B);
            $x3 = $this->field->pow(gmp_add($DA, $CB), 2);
            $z3 = $this->field->mul($x1, $this->field->pow($this->field->sub($DA, $CB), 2));
            $x2 = $this->field->mul($AA, $BB);
            $z2 = $this->field->mul($E, $this->field->add($AA, $this->field->mul($a24, $E)));
        }

        $this->conditionalSwapScalar($x2, $x3, $swap, $this->field->getElementBitLength());
        $this->conditionalSwapScalar($z2, $z3, $swap, $this->field->getElementBitLength());

        return $this->field->mul($x2, gmp_powm($z2, $p2, $this->curve->getP()));
    }
}
