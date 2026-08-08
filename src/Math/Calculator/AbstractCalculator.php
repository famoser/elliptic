<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Primitives\PrimeField;
use Famoser\Elliptic\Primitives\Curve;

/**
 * @template T
 *
 * @implements CalculatorInterface<T>
 */
abstract class AbstractCalculator implements CalculatorInterface
{
    protected readonly PrimeField $field;
    protected readonly PrimeField $nhField;

    public function __construct(protected readonly Curve $curve)
    {
        $this->field = new PrimeField($curve->getP());

        /**
         * reducing the factor by N would be enough, as this is the order of the group
         * however, to defend against small-subgroup attacks (where the point is in the H subgroup instead of the N subgroup),
         * some standards propose reducing the factor by N*H (see NIST Cofactor Diffie-Hellman)
         * https://neilmadden.blog/2020/05/28/whats-the-curve25519-clamping-all-about/
         *
         * as H is 1 for all curves except bernsteins, it does not make a difference for all other curves
         * therefore, we keep it here at N*H
         */
        $this->nhField = new PrimeField(gmp_mul($curve->getN(), $curve->getH()));
    }
}
