<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Primitives\Point;

/**
 * Implements algorithms proposed in https://www.hyperelliptic.org/EFD/g1p/auto-montgom.html
 */
trait MGUnsafeAdder
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
                gmp_sub(
                    $this->field->mul(
                        $this->curve->getB(),
                        $this->field->sq($lambda)
                    ),
                    $this->curve->getA()
                ),
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
                gmp_add(
                    $this->field->mul(
                        gmp_init(3),
                        $this->field->sq($a->x)
                    ),
                    $this->field->mul(
                        gmp_mul(2, $this->curve->getA()),
                        $a->x
                    )
                ),
                1
            ),
            /** @phpstan-ignore-next-line invert may return false; then this will crash (which is OK, because cannot recover anyway) */
            $this->field->invert(
                $this->field->mul(
                    gmp_mul(2, $this->curve->getB()),
                    $a->y
                )
            )
        );

        $x = $this->field->sub(
            gmp_sub(
                $this->field->mul(
                    $this->curve->getB(),
                    $this->field->sq($lambda)
                ),
                $this->curve->getA()
            ),
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
