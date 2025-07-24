<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Primitives\Point;

/**
 * Implements algorithms proposed in https://www.hyperelliptic.org/EFD/g1p/auto-montgom.html
 * Merged with rules for edge-cases from https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
 */
trait EDUnsafeAdder
{
    public function add(Point $a, Point $b): Point
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
            $this->field->invert(gmp_add(1, $lambda)),
        );

        $y = $this->field->mul(
            gmp_sub(
                $this->field->mul($a->y, $b->y),
                $this->field->mul($a->x, $b->x),
            ),
            $this->field->invert(gmp_sub(1, $lambda)),
        );

        return new Point($x, $y);
    }

    public function double(Point $a): Point
    {
        if (gmp_cmp($a->y, 0) === 0) {
            return Point::createInfinity();
        }

        // rule 5
        $lambda = $this->field->mul(
            $this->curve->getB(),
            $this->field->mul(
                $this->field->pow($a->x, 2),
                $this->field->pow($a->y, 2),
            )
        );

        $x = $this->field->mul(
            gmp_add(
                $this->field->mul($a->x, $a->y),
                $this->field->mul($a->y, $a->x),
            ),
            $this->field->invert(gmp_add(1, $lambda)),
        );

        $y = $this->field->mul(
            gmp_sub(
                $this->field->mul($a->y, $a->y),
                $this->field->mul($a->x, $a->x),
            ),
            $this->field->invert(gmp_sub(1, $lambda)),
        );

        return new Point($x, $y);
    }
}
