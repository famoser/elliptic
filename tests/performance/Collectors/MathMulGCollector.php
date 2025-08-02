<?php

namespace Famoser\Elliptic\Tests\Performance\Collectors;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Curve;

class MathMulGCollector extends MulGCollector
{
    public function __construct(string $curveName, Curve $curve, private readonly MathInterface $math)
    {
        $mathName = substr($math::class, strrpos($math::class, '\\') + 1);
        parent::__construct($curveName, $curve, $mathName);
    }

    protected function runMulG(\GMP $factor): void
    {
        $this->math->mulG($factor);
    }
}
