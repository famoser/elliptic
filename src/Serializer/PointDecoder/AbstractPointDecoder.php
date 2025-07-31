<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;

abstract class AbstractPointDecoder implements PointDecoderInterface
{
    public function __construct(private readonly Curve $curve)
    {
    }

    public function getCurve(): Curve
    {
        return $this->curve;
    }
}
