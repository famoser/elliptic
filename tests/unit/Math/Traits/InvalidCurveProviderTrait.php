<?php

namespace Famoser\Elliptic\Tests\Math\Traits;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;

trait InvalidCurveProviderTrait
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

    public static function invalid_MG_Curves(): array
    {
        $curve = BernsteinCurveFactory::curve25519();
        $builder = new CurveBuilder($curve);

        return [
            // wrong because not montgomery
            ...array_map(static fn(CurveBuilder $builder) => [$builder->build()], iterator_to_array($builder->allButType(CurveType::Montgomery)))
        ];
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

    public static function invalid_TwED_Curves(): array
    {
        $curve = BernsteinCurveFactory::edwards25519();
        $builder = new CurveBuilder($curve);

        return [
            // wrong because not montgomery
            ...array_map(static fn(CurveBuilder $builder) => [$builder->build()], iterator_to_array($builder->allButType(CurveType::TwistedEdwards)))
        ];
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
}
