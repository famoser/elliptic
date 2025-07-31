<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

interface PointDecoderInterface
{
    public function getCurve(): Curve;

    public function fromCoordinates(\GMP $x, \GMP $y): Point;
    public function fromXCoordinate(\GMP $x, ?bool $isEvenY = null): Point;
}
