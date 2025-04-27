<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\UnsafePrimeCurveMath;
use Famoser\Elliptic\Primitives\Curve;
use Sop\ASN1\Type\UnspecifiedType;

class EcdhSepkTest extends AbstractEcdhTestCase
{
    public static function provideSecp256k1(): array
    {
        return FixturesRepository::createFilteredEcdhFixtures('secp256k1');
    }

    /**
     * @dataProvider provideSecp256k1
     */
    public function testSecp256k1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        if (str_contains($comment, "The point of the public key is a valid on secp256k1.")) {
            $result = WycheProofConstants::RESULT_VALID;
        }

        if (str_contains($comment, 'using secp224r1') || str_contains($comment, 'using secp256r1')) {
            $comment = parent::POINT_NOT_ON_CURVE_COMMENT_WHITELIST[0];
        }

        $curve = SEC2CurveFactory::secp256k1();
        $math = new UnsafePrimeCurveMath($curve);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    public static function provideBrainpoolP256r1(): array
    {
        return FixturesRepository::createFilteredEcdhFixtures('brainpoolP256r1');
    }

    /**
     * @dataProvider provideBrainpoolP256r1
     */
    public function testBrainpoolP256r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // we do not test the DER deserialization; hence these are fine
        if (str_contains($comment, "a point shared with brainpoolP256r1") || str_contains($comment, "The point of the public key is a valid on brainpoolP256r1")) {
            $result = WycheProofConstants::RESULT_VALID;
        }

        if (str_contains($comment, 'using secp256r1') || str_contains($comment, 'using secp256k1') || str_contains($comment, 'public key on isomorphic curve brainpoolP256t1')) {
            $comment = parent::POINT_NOT_ON_CURVE_COMMENT_WHITELIST[0];
        }

        $curve = BrainpoolCurveFactory::p256r1();
        $twist = BrainpoolCurveFactory::p256r1TwistToP256t1();
        $math = new SW_QT_ANeg3_Math($curve, $twist);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    protected function testCurve(MathInterface $math, string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // unserialize public key from DER format
        $asnObject = UnspecifiedType::fromDER(hex2bin($public));
        $encodedKey = $asnObject->asSequence()->at(1)->asBitString();
        $publicKey = bin2hex($encodedKey->string());

        parent::testCurve($math, $comment, $publicKey, $private, $shared, $result, $flags);
    }
}
