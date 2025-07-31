<?php

namespace Famoser\Elliptic\Tests\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;
use Famoser\Elliptic\Tests\Math\Traits\InvalidCurveProviderTrait;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
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
     * @dataProvider invalid_TwED_Curves
     */
    public function testTwEDPointDecoder(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new TwEDPointDecoder($curve);
    }
}
