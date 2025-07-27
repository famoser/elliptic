<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Primitives\Point;

/**
 * Implements algorithms proposed in https://www.hyperelliptic.org/EFD/g1p/auto-twisted.html
 */
trait TwEDUnsafeAdder
{
    use UnsafeAdderTrait;

    private function addRule4(Point $a, Point $b): Point
    {
        // rule 4 (note that lamba = d*x1*x2*y1*y2)
        $lambda = $this->field->mul(
            $this->curve->getB(),
            $this->field->mul(
                $this->field->mul($a->x, $b->x),
                $this->field->mul($a->y, $b->y),
            )
        );

        $x = $this->field->mul(
            gmp_add(
                $this->field->mul($a->x, $b->y),
                $this->field->mul($a->y, $b->x),
            ),
            /** @phpstan-ignore-next-line  */
            $this->field->invert(gmp_add(1, $lambda)),
        );

        $y = $this->field->mul(
            gmp_sub(
                $this->field->mul($a->y, $b->y),
                $this->field->mul(
                    $this->curve->getA(),
                    $this->field->mul($a->x, $b->x)
                )
            ),
            /** @phpstan-ignore-next-line  */
            $this->field->invert(gmp_sub(1, $lambda)),
        );

        return new Point($x, $y);
    }

    private function doubleRule5(Point $a): Point
    {
        // rule 5
        $lambda = $this->field->mul(
            $this->curve->getB(),
            $this->field->mul(
                $this->field->sq($a->x),
                $this->field->sq($a->y),
            )
        );

        $x = $this->field->mul(
            gmp_add(
                $this->field->mul($a->x, $a->y),
                $this->field->mul($a->y, $a->x),
            ),
            /** @phpstan-ignore-next-line  */
            $this->field->invert(gmp_add(1, $lambda)),
        );

        $y = $this->field->mul(
            gmp_sub(
                $this->field->mul($a->y, $a->y),
                $this->field->mul(
                    $this->curve->getA(),
                    $this->field->mul($a->x, $a->x),
                )
            ),
            /** @phpstan-ignore-next-line  */
            $this->field->invert(gmp_sub(1, $lambda)),
        );

        return new Point($x, $y);
    }
}
