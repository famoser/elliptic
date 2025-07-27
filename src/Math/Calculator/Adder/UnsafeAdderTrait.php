<?php

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Primitives\Point;

/**
 * General edge-cases from https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
 * Then calls curve-specific formulas
 */
trait UnsafeAdderTrait
{
    public function add(Point $a, Point $b): Point
    {
        // rule 1 & 2
        if ($this->isInfinity($a)) {
            return clone $b;
        } elseif ($this->isInfinity($b)) {
            return clone $a;
        }

        if (gmp_cmp($a->x, $b->x) === 0) {
            // rule 3
            if (gmp_cmp($b->y, $a->y) !== 0) {
                return $this->getInfinity();
            }

            // rule 5
            return $this->double($a);
        }

        return $this->addRule4($a, $b);
    }

    public function double(Point $a): Point
    {
        return $this->doubleRule5($a);
    }
}
