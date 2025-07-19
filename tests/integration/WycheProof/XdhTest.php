<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Integration\WycheProof\Traits\DiffieHellmanTrait;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MG_TwED_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use PHPUnit\Framework\TestCase;

class XdhTest extends TestCase
{
    use DiffieHellmanTrait;

    public static function provideCurve25519(): array
    {
        return FixturesRepository::createFilteredXdhFixtures('x25519');
    }

    /**
     * @dataProvider provideCurve25519
     */
    public function testCurve25519(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        if (in_array(WycheProofConstants::FLAG_TWIST, $flags, true)) {
            $this->markTestSkipped("Public key on twist; not supported.");
        }
        if (in_array($public, ['ecffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', 'ecffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff7f'], true)) {
            $this->markTestSkipped("Cannot recover y coordinate of point (Jacobi symbol of alpha = -1).");
        }
        $curve = BernsteinCurveFactory::curve25519();
        $math = new MGUnsafeMath($curve);

        $encoder = new RFC7784Decoder();
        $publicU = $encoder->decodeUCoordinate($public, 255);

        $pointDecoder = new MGPointDecoder($curve);
        $publicPoint = $pointDecoder->fromXCoordinate($publicU);

        $decodedPrivate = $encoder->decodeScalar25519($private);
        $decodedShared = $encoder->decodeScalar25519($shared);

        $this->assertDHCorrect($math, $publicPoint, $decodedPrivate, $decodedShared, $result);
    }

    /**
     * @dataProvider provideCurve25519
     */
    public function testCurve25519TwistedEdwards(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        if (in_array(WycheProofConstants::FLAG_TWIST, $flags, true)) {
            $this->markTestSkipped("Public key on twist; not supported.");
        }
        if (
            in_array($public, ['ecffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', 'ecffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff7f'], true)
            && $result !== WycheProofConstants::RESULT_VALID
        ) {
            $this->markTestSkipped("Cannot recover y coordinate of point (Jacobi symbol of alpha = -1).");
        }
        if (
            in_array($public, ['0000000000000000000000000000000000000000000000000000000000000000', '0100000000000000000000000000000000000000000000000000000000000000', 'edffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff7f', 'eeffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff7f', '0000000000000000000000000000000000000000000000000000000000000080', 'edffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff'], true)
            && $result !== WycheProofConstants::RESULT_VALID
        ) {
            $this->markTestSkipped("Mapping to TwistedEdwards is undefined.");
        }
        if ($public === 'e0eb7a7c3b41b8ae1656e3faf19fc46ada098deb9c32b1fd866205165f49b800' && $result !== WycheProofConstants::RESULT_VALID) {
            // unclear why exactly this is not supported. however, does also use a crafted public key with a low order
            // hence consumers of math need to validate the public key, or accept undefined behavior
            $this->markTestSkipped("Mapping back from TwistedEdwards is undefined.");
        }
        $curve = BernsteinCurveFactory::curve25519();
        $map = BernsteinCurveFactory::curve25519ToEdwards25519();
        $targetCurve = BernsteinCurveFactory::edwards25519();
        $math = new MG_TwED_Math($curve, $map, $targetCurve);

        $encoder = new RFC7784Decoder();
        $publicU = $encoder->decodeUCoordinate($public, 255);

        $pointDecoder = new MGPointDecoder($curve);
        $publicPoint = $pointDecoder->fromXCoordinate($publicU);

        $decodedPrivate = $encoder->decodeScalar25519($private);
        $decodedShared = $encoder->decodeScalar25519($shared);

        $this->assertDHCorrect($math, $publicPoint, $decodedPrivate, $decodedShared, $result);
    }
}
