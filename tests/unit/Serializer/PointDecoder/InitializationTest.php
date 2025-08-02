<?php

namespace Famoser\Elliptic\Tests\Unit\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Serializer\PointDecoder\EDPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;
use Famoser\Elliptic\Tests\Unit\Math\Traits\InvalidCurveProviderTrait;
use PHPUnit\Framework\TestCase;

class InitializationTest extends TestCase
{
    use InvalidCurveProviderTrait;

    /**
     * @dataProvider invalid_MG_Curves
     */
    public function testMGPointDecoder(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new MGPointDecoder($curve);
    }

    /**
     * @dataProvider invalid_SW_Curves
     */
    public function testSWPointDecoder(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new SWPointDecoder($curve);
    }

    /**
     * @dataProvider invalid_ED_Curves
     */
    public function testEDPointDecoder(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new EDPointDecoder($curve);
    }

    /**
     * @dataProvider invalid_TwED_Curves
     */
    public function testTwEDPointDecoder(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new TwEDPointDecoder($curve);
    }
}
