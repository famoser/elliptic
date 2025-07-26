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
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class MathInitializationTest extends TestCase
{
    public static function invalid_ED_Curves(): array
    {
        $curve = BernsteinCurveFactory::edwards448();
        $builder = new CurveBuilder($curve);

        return [
            // wrong because not montgomery
            ...array_map(static fn(CurveBuilder $builder) => [$builder->build()], iterator_to_array($builder->allButType(CurveType::Edwards)))
        ];
    }

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

    public static function invalid_MG_Curves(): array
    {
        $curve = BernsteinCurveFactory::curve25519();
        $builder = new CurveBuilder($curve);

        return [
            // wrong because not montgomery
            ...array_map(static fn(CurveBuilder $builder) => [$builder->build()], iterator_to_array($builder->allButType(CurveType::Montgomery)))
        ];
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

    public static function invalid_SW_ANeg3_Curves(): array
    {
        $curve = SEC2CurveFactory::secp192r1();
        $builder = new CurveBuilder($curve);
        $aPlusOne = gmp_add($curve->getA(), 1);

        return [
            // wrong because a != -3
            [$builder->withA($aPlusOne)->build()],
            // wrong because not montgomery
            ...array_map(static fn(CurveBuilder $builder) => [$builder->build()], iterator_to_array($builder->allButType(CurveType::ShortWeierstrass))),
            // wrong because both untrue
            ...array_map(static fn(CurveBuilder $builder) => [$builder->withA($aPlusOne)->build()], iterator_to_array($builder->allButType(CurveType::ShortWeierstrass)))
        ];
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

    public static function invalid_SW_Curves(): array
    {
        $curve = SEC2CurveFactory::secp384r1();
        $builder = new CurveBuilder($curve);

        return [
            // wrong because not montgomery
            ...array_map(static fn(CurveBuilder $builder) => [$builder->build()], iterator_to_array($builder->allButType(CurveType::ShortWeierstrass)))
        ];
    }

    /**
     * @dataProvider invalid_SW_Curves
     */
    public function test_SWUnsafeMath(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new SWUnsafeMath($curve);
    }

    public static function invalid_TwED_ANeg1_Curves(): array
    {
        $curve = BernsteinCurveFactory::curve25519();
        $builder = new CurveBuilder($curve);
        $aPlusOne = gmp_add($curve->getA(), 1);

        return [
            // wrong because a != -1
            [$builder->withA($aPlusOne)->build()],
            // wrong because not montgomery
            ...array_map(static fn(CurveBuilder $builder) => [$builder->build()], iterator_to_array($builder->allButType(CurveType::Edwards))),
            // wrong because both untrue
            ...array_map(static fn(CurveBuilder $builder) => [$builder->withA($aPlusOne)->build()], iterator_to_array($builder->allButType(CurveType::Edwards)))
        ];
    }

    /**
     * @dataProvider invalid_TwED_ANeg1_Curves
     */
    public function test_TwED_ANeg1_Math(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new TwED_ANeg1_Math($curve);
    }

    public static function invalid_TwED_Curves(): array
    {
        $curve = BernsteinCurveFactory::edwards25519();
        $builder = new CurveBuilder($curve);

        return [
            // wrong because not montgomery
            ...array_map(static fn(CurveBuilder $builder) => [$builder->build()], iterator_to_array($builder->allButType(CurveType::TwistedEdwards)))
        ];
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
