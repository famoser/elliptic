<?php

namespace Famoser\Elliptic\Math\Calculator\Swapper;

use Famoser\Elliptic\Primitives\Point;

trait PointSwapper
{
    use ScalarSwapper;

    public function conditionalSwap(Point $a, Point $b, int $swapBit): void
    {
        $this->conditionalSwapScalar($a->x, $b->x, $swapBit, $this->field->getElementBitLength());
        $this->conditionalSwapScalar($a->y, $b->y, $swapBit, $this->field->getElementBitLength());
    }
}
