<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class PrimitivesConsistencyCheck extends TestCase
{
    public function testPointInfinity(): void
    {
        $point = Point::createInfinity();
        $this->assertTrue($point->isInfinity());
    }

    public function testJacobiPointInfinity(): void
    {
        $point = JacobiPoint::createInfinity();
        $this->assertTrue($point->isInfinity());
    }
}
