<?php

namespace Famoser\Elliptic\Tests\Math\Primitives;

use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Math\Primitives\ProjectiveCoordinates;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class InfinityTest extends TestCase
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

    public function testProjectiveCoordinatesPointInfinity(): void
    {
        $point = ProjectiveCoordinates::createInfinity();
        $this->assertTrue($point->isInfinity());
    }
}
