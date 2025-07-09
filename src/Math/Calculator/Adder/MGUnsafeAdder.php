<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Primitives\Point;

/**
 * Implements algorithms proposed in https://www.hyperelliptic.org/EFD/g1p/auto-montgom.html
 * Merged with rules for edge-cases from https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
 */
trait MGUnsafeAdder
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

        // rule 4 (note that a / b = a * b^-1)
        $lambda = $this->field->mul(
            gmp_sub($b->y, $a->y),
            /** @phpstan-ignore-next-line invert may return false; then this will crash (which is OK, because cannot recover anyway) */
            $this->field->invert(gmp_sub($b->x, $a->x))
        );

        $x = $this->field->sub(
            gmp_sub(
                gmp_sub(
                    gmp_mul(
                        $this->curve->getB(),
                        gmp_pow($lambda, 2)
                    ),
                    $this->curve->getA()
                ),
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

    public function double(Point $a): Point
    {
        if (gmp_cmp($a->y, 0) === 0) {
            return Point::createInfinity();
        }

        // rule 5 (note that a / b = a * b^-1)
        $lambda = $this->field->mul(
            gmp_add(
                gmp_add(
                    gmp_mul(
                        gmp_init(3),
                        gmp_pow($a->x, 2)
                    ),
                    gmp_mul(
                        gmp_mul(2, $this->curve->getA()),
                        $a->x
                    )
                ),
                1
            ),
            /** @phpstan-ignore-next-line invert may return false; then this will crash (which is OK, because cannot recover anyway) */
            $this->field->invert(
                gmp_mul(
                    gmp_mul(2, $this->curve->getB()),
                    $a->y
                )
            )
        );

        $x = $this->field->sub(
            gmp_sub(
                gmp_mul(
                    $this->curve->getB(),
                    gmp_pow($lambda, 2)
                ),
                $this->curve->getA()
            ),
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
