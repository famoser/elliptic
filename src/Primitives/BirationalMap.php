<?php

namespace Famoser\Elliptic\Primitives;

use Famoser\Elliptic\Math\MathInterface;

class BirationalMap
{
    /**
     * @param \Closure(MathInterface, Point): Point $map
     * @param \Closure(MathInterface, Point): Point $reverse
     */
    public function __construct(private readonly \Closure $map, private readonly \Closure $reverse)
    {
    }

    public function map(MathInterface $math, Point $point): Point
    {
        return ($this->map)($math, $point);
    }

    public function reverse(MathInterface $math, Point $point): Point
    {
        return ($this->reverse)($math, $point);
    }
}
