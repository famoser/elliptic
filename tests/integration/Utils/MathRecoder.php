<?php

namespace Famoser\Elliptic\Tests\Integration\Utils;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

class MathRecoder implements MathInterface
{
    private int|string $context = 0;
    private array $operations = [];

    public function __construct(private readonly MathInterface $math)
    {
    }

    public function getMath(): MathInterface
    {
        return $this->math;
    }

    public function getCurve(): Curve
    {
        $this->operations[$this->context][] = ['getCurve'];
        return $this->math->getCurve();
    }

    public function isInfinity(Point $point): bool
    {
        $this->operations[$this->context][] = ['isInfinity', [$point]];
        return $this->math->isInfinity($point);
    }

    public function getInfinity(): Point
    {
        $this->operations[$this->context][] = ['getInfinity'];
        return $this->math->getInfinity();
    }

    public function double(Point $a): Point
    {
        $this->operations[$this->context][] = ['double', [$a]];
        return $this->math->double($a);
    }

    public function add(Point $a, Point $b): Point
    {
        $this->operations[$this->context][] = ['add', [$a, $b]];
        return $this->math->double($a);
    }

    public function mulG(\GMP $factor): Point
    {
        $this->operations[$this->context][] = ['mulG', [$factor]];
        return $this->math->mulG($factor);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $this->operations[$this->context][] = ['mul', [$point, $factor]];
        return $this->math->mul($point, $factor);
    }

    public function setContext(int|string $key): void
    {
        $this->context = $key;
        if (!isset($this->operations[$this->context])) {
            $this->operations[$this->context] = [];
        }
    }

    public function getOperations(): array
    {
        return $this->operations;
    }
}
