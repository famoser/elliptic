<?php

namespace Famoser\Elliptic\Serializer\SEC;

use Famoser\Elliptic\Primitives\Point;

interface SECPointDecoderInterface
{
    public function fromCoordinates(\GMP $x, \GMP $y): Point;
    public function fromXCoordinate(\GMP $x, bool $isEvenY): Point;
}
