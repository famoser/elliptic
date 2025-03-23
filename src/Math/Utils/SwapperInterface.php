<?php

namespace Famoser\Elliptic\Math\Utils;

interface SwapperInterface
{
    public function conditionalSwap(\GMP &$a, \GMP &$b, int $swapBit, int $maxBitLength): void;
}
