<?php

namespace Famoser\Elliptic\Tests\Performance\Collectors;

use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Primitives\Curve;

class MGXCalculatorMulGCollector extends MulGCollector
{
    private MGXCalculator $calculator;

    public function __construct(string $curveName, private readonly Curve $curve)
    {
        $this->calculator = new MGXCalculator($curve);
        parent::__construct($curveName, $curve, 'MGXCalculator');
    }

    protected function runMulG(\GMP $factor): void
    {
        $this->calculator->mul($this->curve->getG()->x, $factor);
    }
}
