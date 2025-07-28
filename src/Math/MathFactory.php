<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;

class MathFactory
{
    public function __construct(private readonly CurveRepository $curveRepository)
    {
    }

    public function createUnsafeMath(Curve $curve): MathInterface
    {
        return match ($curve->getType()) {
            CurveType::ShortWeierstrass => new SWUnsafeMath($curve),
            CurveType::Montgomery => new MGUnsafeMath($curve),
            CurveType::TwistedEdwards => new TwEDUnsafeMath($curve),
            CurveType::Edwards => new EDUnsafeMath($curve)
        };
    }

    public function createHardenedMath(Curve $curve): ?MathInterface
    {
        $curveName = $this->curveRepository->getCanonicalName($curve);

        return match ($curveName) {
            'secp192r1', 'secp224r1', 'secp256r1', 'secp384r1', 'secp521r1',
            'brainpoolP160t1', 'brainpoolP192t1', 'brainpoolP224t1', 'brainpoolP256t1',
            'brainpoolP320t1', 'brainpoolP384t1', 'brainpoolP512t1' => new SW_ANeg3_Math($curve),

            'brainpoolP160r1' => new SW_QT_ANeg3_Math($curve, BrainpoolCurveFactory::p160r1TwistToP160t1()),
            'brainpoolP192r1' => new SW_QT_ANeg3_Math($curve, BrainpoolCurveFactory::p192r1TwistToP192t1()),
            'brainpoolP224r1' => new SW_QT_ANeg3_Math($curve, BrainpoolCurveFactory::p224r1TwistToP224t1()),
            'brainpoolP256r1' => new SW_QT_ANeg3_Math($curve, BrainpoolCurveFactory::p256r1TwistToP256t1()),
            'brainpoolP320r1' => new SW_QT_ANeg3_Math($curve, BrainpoolCurveFactory::p320r1TwistToP320t1()),
            'brainpoolP384r1' => new SW_QT_ANeg3_Math($curve, BrainpoolCurveFactory::p384r1TwistToP384t1()),
            'brainpoolP512r1' => new SW_QT_ANeg3_Math($curve, BrainpoolCurveFactory::p512r1TwistToP512t1()),

            /** @phpstan-ignore-next-line */
            'curve25519' => new MG_TwED_ANeg1_Math($curve, BernsteinCurveFactory::curve25519ToEdwards25519(), $this->curveRepository->findByName('edwards25519')),
            /** @phpstan-ignore-next-line */
            'curve448' => new MG_ED_Math($curve, BernsteinCurveFactory::curve448ToEdwards(), $this->curveRepository->findByName('curve448Edwards')),
            'edwards25519' => new TwED_ANeg1_Math($curve),
            'edwards448', 'curve448Edwards' => new EDMath($curve),

            default => null,
        };
    }
}
