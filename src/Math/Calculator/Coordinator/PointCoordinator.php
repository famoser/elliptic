<?php

namespace Famoser\Elliptic\Math\Calculator\Coordinator;

use Famoser\Elliptic\Primitives\Point;

trait PointCoordinator
{
    public function getInfinity(): Point
    {
        return Point::createInfinity();
    }
}
