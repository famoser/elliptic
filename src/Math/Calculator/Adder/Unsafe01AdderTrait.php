<?php

namespace Famoser\Elliptic\Math\Calculator\Adder;

use Famoser\Elliptic\Primitives\Point;

/**
 * Rules inspired by https://www.secg.org/SEC1-Ver-1.0.pdf (2.2.1)
 * But rule 3 is excluded, as the negation rule does not apply to 01 infinity curves (twisted edwards, edwards)
 *
 */
trait Unsafe01AdderTrait
{
    public function add(Point $a, Point $b): Point
    {
        // rule 1 & 2
        if ($this->isInfinity($a)) {
            return clone $b;
        } elseif ($this->isInfinity($b)) {
            return clone $a;
        }

        return $this->addRule4($a, $b);
    }

    public function double(Point $a): Point
    {
        return $this->doubleRule5($a);
    }
}
