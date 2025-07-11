<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Integration\Utils\AsnEncoder;
use Famoser\Elliptic\Integration\WycheProof\Traits\DiffieHellmanTrait;
use Famoser\Elliptic\Integration\WycheProof\Traits\EncodedPointTrait;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use PHPUnit\Framework\TestCase;

class EcdhAsnTest extends TestCase
{
    use EncodedPointTrait;
    use DiffieHellmanTrait;

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
            $comment = WycheProofConstants::POINT_DECODING_FAIL_COMMENT_WHITELIST[0];
        }

        $curve = SEC2CurveFactory::secp256k1();
        $math = new SWUnsafeMath($curve);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    public static function provideBrainpoolP224r1(): array
    {
        return FixturesRepository::createFilteredEcdhFixtures('brainpoolP224r1');
    }

    /**
     * @dataProvider provideBrainpoolP224r1
     */
    public function testBrainpoolP224r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // we do not test the DER deserialization; hence these are fine
        if (str_contains($comment, "a point shared with brainpoolP224r1") || str_contains($comment, "The point of the public key is a valid on brainpoolP224r1")) {
            $result = WycheProofConstants::RESULT_VALID;
        }

        if (str_contains($comment, 'using secp224r1') || str_contains($comment, 'using secp224k1') || str_contains($comment, 'public key on isomorphic curve brainpoolP224t1')) {
            $comment = WycheProofConstants::POINT_DECODING_FAIL_COMMENT_WHITELIST[0];
        }

        $curve = BrainpoolCurveFactory::p224r1();
        $twist = BrainpoolCurveFactory::p224r1TwistToP224t1();
        $math = new SW_QT_ANeg3_Math($curve, $twist);
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
            $comment = WycheProofConstants::POINT_DECODING_FAIL_COMMENT_WHITELIST[0];
        }

        $curve = BrainpoolCurveFactory::p256r1();
        $twist = BrainpoolCurveFactory::p256r1TwistToP256t1();
        $math = new SW_QT_ANeg3_Math($curve, $twist);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    public static function provideBrainpoolP320r1(): array
    {
        return FixturesRepository::createFilteredEcdhFixtures('brainpoolP320r1');
    }

    /**
     * @dataProvider provideBrainpoolP320r1
     */
    public function testBrainpoolP320r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // we do not test the DER deserialization; hence these are fine
        if (str_contains($comment, "a point shared with brainpoolP320r1") || str_contains($comment, "The point of the public key is a valid on brainpoolP320r1")) {
            $result = WycheProofConstants::RESULT_VALID;
        }

        if (str_contains($comment, 'public key on isomorphic curve brainpoolP320t1')) {
            $comment = WycheProofConstants::POINT_DECODING_FAIL_COMMENT_WHITELIST[0];
        }

        $curve = BrainpoolCurveFactory::p320r1();
        $twist = BrainpoolCurveFactory::p320r1TwistToP320t1();
        $math = new SW_QT_ANeg3_Math($curve, $twist);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    public static function provideBrainpoolP384r1(): array
    {
        return FixturesRepository::createFilteredEcdhFixtures('brainpoolP384r1');
    }

    /**
     * @dataProvider provideBrainpoolP384r1
     */
    public function testBrainpoolP384r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // we do not test the DER deserialization; hence these are fine
        if (str_contains($comment, "a point shared with brainpoolP384r1") || str_contains($comment, "The point of the public key is a valid on brainpoolP384r1")) {
            $result = WycheProofConstants::RESULT_VALID;
        }

        if (str_contains($comment, 'public key on isomorphic curve brainpoolP384t1')) {
            $comment = WycheProofConstants::POINT_DECODING_FAIL_COMMENT_WHITELIST[0];
        }

        $curve = BrainpoolCurveFactory::p384r1();
        $twist = BrainpoolCurveFactory::p384r1TwistToP384t1();
        $math = new SW_QT_ANeg3_Math($curve, $twist);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    public static function provideBrainpoolP512r1(): array
    {
        return FixturesRepository::createFilteredEcdhFixtures('brainpoolP512r1');
    }

    /**
     * @dataProvider provideBrainpoolP512r1
     */
    public function testBrainpoolP512r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // we do not test the DER deserialization; hence these are fine
        if (str_contains($comment, "a point shared with brainpoolP512r1") || str_contains($comment, "The point of the public key is a valid on brainpoolP512r1")) {
            $result = WycheProofConstants::RESULT_VALID;
        }

        if (str_contains($comment, 'public key on isomorphic curve brainpoolP512t1')) {
            $comment = WycheProofConstants::POINT_DECODING_FAIL_COMMENT_WHITELIST[0];
        }

        $curve = BrainpoolCurveFactory::p512r1();
        $twist = BrainpoolCurveFactory::p512r1TwistToP512t1();
        $math = new SW_QT_ANeg3_Math($curve, $twist);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    protected function testCurve(MathInterface $math, string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $asnEncoder = new AsnEncoder();
        $publicKey = $asnEncoder->decodePublicKey($public);

        $this->assertPublicKeyPointDecodes($math, $comment, $publicKey, $result, $flags, $publicKeyPoint);
        $this->assertDHCorrect($math, $publicKeyPoint, $private, $shared, $result);
    }
}
