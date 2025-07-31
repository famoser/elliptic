<?php

namespace Famoser\Elliptic\Tests\Math;

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
use Famoser\Elliptic\Tests\Math\Traits\InvalidCurveProviderTrait;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class InitializationTest extends TestCase
{
    use InvalidCurveProviderTrait;

    /**
     * @dataProvider invalid_ED_Curves
     */
    public function test_EDMath(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new EDMath($curve);
    }

    /**
     * @dataProvider invalid_ED_Curves
     */
    public function test_EDUnsafeMath(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new EDUnsafeMath($curve);
    }

    /**
     * @dataProvider invalid_ED_Curves
     */
    public function test_MG_ED_Math(Curve $targetCurve): void
    {
        $this->expectException(\AssertionError::class);
        $curve = BernsteinCurveFactory::curve25519();
        $mapping = BernsteinCurveFactory::curve25519ToEdwards25519();

        new MG_ED_Math($curve, $mapping, $targetCurve);
    }

    /**
     * @dataProvider invalid_TwED_ANeg1_Curves
     */
    public function test_MG_TwED_ANeg1_Math(Curve $targetCurve): void
    {
        $this->expectException(\AssertionError::class);
        $curve = BernsteinCurveFactory::curve25519();
        $mapping = BernsteinCurveFactory::curve25519ToEdwards25519();

        new MG_TwED_ANeg1_Math($curve, $mapping, $targetCurve);
    }

    /**
     * @dataProvider invalid_MG_Curves
     */
    public function test_MGUnsafeMath(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new MGUnsafeMath($curve);
    }

    /**
     * @dataProvider invalid_SW_ANeg3_Curves
     */
    public function test_SW_ANeg3_Math(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new SW_ANeg3_Math($curve);
    }

    /**
     * @dataProvider invalid_SW_ANeg3_Curves
     */
    public function test_SW_QT_ANeg3_Math(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);
        $twist = BrainpoolCurveFactory::p224r1TwistToP224t1();

        new SW_QT_ANeg3_Math($curve, $twist);
    }

    /**
     * @dataProvider invalid_SW_Curves
     */
    public function test_SWUnsafeMath(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new SWUnsafeMath($curve);
    }

    /**
     * @dataProvider invalid_TwED_ANeg1_Curves
     */
    public function test_TwED_ANeg1_Math(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new TwED_ANeg1_Math($curve);
    }

    /**
     * @dataProvider invalid_TwED_Curves
     */
    public function test_TwEDUnsafeMath(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new TwEDUnsafeMath($curve);
    }
}
