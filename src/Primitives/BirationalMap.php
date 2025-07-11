<?php

namespace Famoser\Elliptic\Primitives;

class BirationalMap
{
    /**
     * @param \Closure(Point): Point $map
     * @param \Closure(Point): Point $reverse
     */
    public function __construct(private readonly \Closure $map, private readonly \Closure $reverse)
    {
    }

    public function map(Point $point): Point
    {
        return ($this->map)($point);
    }

    public function reverse(Point $point): Point
    {
        return ($this->reverse)($point);
    }
}
