<?php

namespace Famoser\Elliptic\Math\Calculator\Swapper;

use Famoser\Elliptic\Math\Primitives\ExtendedCoordinates;

trait ExtendedSwapper
{
    use ScalarSwapper;

    public function conditionalSwap(ExtendedCoordinates $a, ExtendedCoordinates $b, int $swapBit): void
    {
        $this->conditionalSwapScalar($a->X, $b->X, $swapBit, $this->field->getElementBitLength());
        $this->conditionalSwapScalar($a->Y, $b->Y, $swapBit, $this->field->getElementBitLength());
        $this->conditionalSwapScalar($a->Z, $b->Z, $swapBit, $this->field->getElementBitLength());
        $this->conditionalSwapScalar($a->T, $b->T, $swapBit, $this->field->getElementBitLength());
    }
}
