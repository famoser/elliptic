<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\EdwardsPointDecoderTrait;

class EDPointDecoder extends AbstractPointDecoder implements PointYDecoderInterface
{
    use EdwardsPointDecoderTrait;

    public function __construct(private readonly Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this decoder
        $check = $curve->getType() === CurveType::Edwards;
        if (!$check) {
            throw new \AssertionError('Cannot use this decoder with the chosen curve.');
        }
    }
}
