<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Tests\Integration\WycheProof;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use PHPUnit\Framework\TestCase;

class XdhTest extends TestCase
{
    public static function provideCurve25519(): array
    {
        return FixturesRepository::createFilteredXdhFixtures('x25519');
    }

    /**
     * @dataProvider provideCurve25519
     */
    public function testCurve25519WithCalculator(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $calculator = new MGXCalculator($curve);

        $encoder = new RFC7784Decoder();
        $publicU = $encoder->decodeUCoordinate($public, 255);
        $decodedPrivate = $encoder->decodeScalar25519($private);
        $sharedSecret = $calculator->mul($publicU, $decodedPrivate);

        $actualSharedSecret = $encoder->encodeUCoordinate($sharedSecret, 255);
        $this->assertEquals($shared, $actualSharedSecret);
        $this->assertNotEquals($result, WycheProofConstants::RESULT_INVALID);
    }

    /**
     * @dataProvider provideCurve25519
     */
    public function testCurve25519MathComparison(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        if (in_array(WycheProofConstants::FLAG_TWIST, $flags, true)) {
            $this->markTestSkipped("Public key on twist; not supported.");
        }

        $curve = BernsteinCurveFactory::curve25519();
        $map = BernsteinCurveFactory::curve25519ToEdwards25519();
        $targetCurve = BernsteinCurveFactory::edwards25519();
        $math = new MG_TwED_ANeg1_Math($curve, $map, $targetCurve);
        $unsafeMath = new MGUnsafeMath($curve);
        $rfcMath = new MGXCalculator($curve);

        $encoder = new RFC7784Decoder();
        $publicU = $encoder->decodeUCoordinate($public, 255);
        $decodedPrivate = $encoder->decodeScalar25519($private);

        try {
            $pointDecoder = new MGPointDecoder($curve);
            $publicPoint = $pointDecoder->fromXCoordinate($publicU);
        } catch (PointDecoderException) {
            $this->markTestSkipped();
        }

        $sharedSecretBaseline = $unsafeMath->mul($publicPoint, $decodedPrivate);
        try {
            $sharedSecret = $math->mul($publicPoint, $decodedPrivate);
        } catch (\TypeError) {
            $this->assertNotEquals($result, WycheProofConstants::RESULT_VALID);
            return;
        }

        $rfc = $rfcMath->mul($publicPoint->x, $decodedPrivate);
        $actualSharedSecret = $encoder->encodeUCoordinate($rfc, 255);
        $this->assertEquals($shared, $actualSharedSecret);

        $this->assertEquals(0, gmp_cmp($rfc, $sharedSecretBaseline->x));
        if (gmp_cmp($rfc, 0) === 0) {
            $this->assertTrue($math->isInfinity($sharedSecret));
        } else {
            $this->assertTrue($sharedSecretBaseline->equals($sharedSecret));
        }
    }

    public static function provideCurve448(): array
    {
        return FixturesRepository::createFilteredXdhFixtures('x448');
    }

    /**
     * @dataProvider provideCurve448
     */
    public function testCurve448WithCalculator(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $curve = BernsteinCurveFactory::curve448();
        $calculator = new MGXCalculator($curve);

        $encoder = new RFC7784Decoder();
        $publicU = $encoder->decodeUCoordinate($public, 448);
        $decodedPrivate = $encoder->decodeScalar448($private);
        $sharedSecret = $calculator->mul($publicU, $decodedPrivate);

        $actualSharedSecret = $encoder->encodeUCoordinate($sharedSecret, 448);
        $this->assertEquals($shared, $actualSharedSecret);
        $this->assertNotEquals($result, WycheProofConstants::RESULT_INVALID);
    }

    /**
     * @dataProvider provideCurve448
     */
    public function testCurve448MathComparison(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        if (in_array(WycheProofConstants::FLAG_TWIST, $flags, true)) {
            $this->markTestSkipped("Public key on twist; not supported.");
        }

        $curve = BernsteinCurveFactory::curve448();
        $map = BernsteinCurveFactory::curve448ToEdwards();
        $targetCurve = BernsteinCurveFactory::curve448Edwards();
        $unsafeMath = new MGUnsafeMath($curve);
        $math = new MG_ED_Math($curve, $map, $targetCurve);
        $rfcMath = new MGXCalculator($curve);

        $encoder = new RFC7784Decoder();
        $publicU = $encoder->decodeUCoordinate($public, 448);
        $decodedPrivate = $encoder->decodeScalar448($private);

        try {
            $pointDecoder = new MGPointDecoder($curve);
            $publicPoint = $pointDecoder->fromXCoordinate($publicU, true);
        } catch (PointDecoderException) {
            $this->markTestSkipped();
        }

        $sharedSecretBaseline = $unsafeMath->mul($publicPoint, $decodedPrivate);
        try {
            $sharedSecret = $math->mul($publicPoint, $decodedPrivate);
        } catch (\TypeError) {
            $this->assertNotEquals($result, WycheProofConstants::RESULT_VALID);
            return;
        }

        $rfc = $rfcMath->mul($publicPoint->x, $decodedPrivate);
        $this->assertEquals(0, gmp_cmp($rfc, $sharedSecretBaseline->x));
        /* TODO: result is mostly wrong from "real" math
        if (gmp_cmp($rfc, 0) === 0) {
            $this->assertTrue($math->isInfinity($sharedSecret));
        } else {
            $this->assertTrue($sharedSecretBaseline->equals($sharedSecret));
        }
        */
    }
}
