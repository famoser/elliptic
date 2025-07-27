<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Primitives\Point;

/**
 * Implements algorithms proposed in https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
 */
trait SWUnsafeAdder
{
    use UnsafeAdderTrait;

    private function addRule4(Point $a, Point $b): Point
    {
        // rule 4 (note that a / b = a * b^-1)
        $lambda = $this->field->mul(
            gmp_sub($b->y, $a->y),
            /** @phpstan-ignore-next-line invert may return false; then this will crash (which is OK, because cannot recover anyway) */
            $this->field->invert(gmp_sub($b->x, $a->x))
        );

        $x = $this->field->sub(
            gmp_sub(
                $this->field->sq($lambda),
                $a->x
            ),
            $b->x
        );

        $y = $this->field->sub(
            $this->field->mul(
                $lambda,
                gmp_sub($a->x, $x)
            ),
            $a->y
        );

        return new Point($x, $y);
    }

    private function doubleRule5(Point $a): Point
    {
        if (gmp_cmp($a->y, 0) === 0) {
            return $this->getInfinity();
        }

        // rule 5 (note that a / b = a * b^-1)
        $lambda = $this->field->mul(
            gmp_add(
                $this->field->mul(
                    gmp_init(3),
                    $this->field->sq($a->x)
                ),
                $this->curve->getA()
            ),
            /** @phpstan-ignore-next-line invert may return false; then this will crash (which is OK, because cannot recover anyway) */
            $this->field->invert(
                gmp_mul(2, $a->y)
            )
        );

        $x = $this->field->sub(
            $this->field->sq($lambda),
            gmp_mul(2, $a->x)
        );

        $y = $this->field->sub(
            $this->field->mul(
                $lambda,
                gmp_sub($a->x, $x)
            ),
            $a->y
        );

        return new Point($x, $y);
    }
}
