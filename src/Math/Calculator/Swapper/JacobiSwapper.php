<?php

namespace Famoser\Elliptic\Math\Calculator\Swapper;

use Famoser\Elliptic\Math\Primitives\JacobiPoint;

trait JacobiSwapper
{
    use ScalarSwapper;

    public function conditionalSwap(JacobiPoint $a, JacobiPoint $b, int $swapBit): void
    {
        $this->conditionalSwapScalar($a->X, $b->X, $swapBit, $this->field->getElementBitLength());
        $this->conditionalSwapScalar($a->Y, $b->Y, $swapBit, $this->field->getElementBitLength());
        $this->conditionalSwapScalar($a->Z, $b->Z, $swapBit, $this->field->getElementBitLength());
    }
}
