<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

interface PointYDecoderInterface
{
    public function fromYCoordinate(\GMP $y, ?bool $isEvenX = null): Point;
}
