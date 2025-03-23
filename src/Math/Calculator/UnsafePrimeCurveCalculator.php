<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Base\AbstractPointCalculator;
use Famoser\Elliptic\Math\Calculator\Base\CalculatorInterface;
use Famoser\Elliptic\Math\Calculator\Primitives\PrimeField;
use Famoser\Elliptic\Math\Utils\SwapperInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * Implements algorithms proposed in https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
 *
 * @implements CalculatorInterface<Point>
 */
class UnsafePrimeCurveCalculator extends AbstractPointCalculator implements CalculatorInterface
{
    private readonly PrimeField $field;

    public function __construct(private readonly Curve $curve, SwapperInterface $swapper)
    {
        $this->field = new PrimeField($curve->getP());
        parent::__construct($this->curve, $swapper, $this->field);

        // check allowed to use this calculator
        $check = in_array($curve->getType(), [CurveType::ShortWeierstrass, CurveType::Montgomery], true);
        if (!$check) {
            throw new \AssertionError('Cannot use this calculator with the chosen curve.');
        }
    }

    /**
     * rules from https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
     */
    public function add(mixed $a, mixed $b): Point
    {
        // rule 1 & 2
        if ($a->isInfinity()) {
            return clone $b;
        } elseif ($b->isInfinity()) {
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
                $a->x
            ),
            $b->x
        );

        $y = $this->field->sub(
            gmp_mul(
                $lambda,
                gmp_sub($a->x, $x)
            ),
            $a->y
        );

        return new Point($x, $y);
    }

    public function double(mixed $a): Point
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
                gmp_mul(2, $a->y)
            )
        );

        $x = $this->field->sub(
            gmp_pow($lambda, 2),
            gmp_mul(2, $a->x)
        );

        $y = $this->field->sub(
            gmp_mul(
                $lambda,
                gmp_sub($a->x, $x)
            ),
            $a->y
        );

        return new Point($x, $y);
    }
}
