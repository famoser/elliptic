<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Integration\Utils\EdDSA\EdDSASignerEd25519;
use Famoser\Elliptic\Integration\Utils\EdDSA\EDDSASignerEd448;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use PHPUnit\Framework\TestCase;

class EddsaTest extends TestCase
{
    public static function provideEd25519(): array
    {
        return FixturesRepository::createEddsaFixtures('eddsa');
    }

    /**
     * @dataProvider provideEd25519
     */
    public function testEd25519UnsafeMath(string $public, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        $math = new TwEDUnsafeMath(BernsteinCurveFactory::edwards25519());

        $this->verifyEd25519Signature($math, $public, $signature, $message, $result);
    }

    /**
     * @dataProvider provideEd25519
     */
    public function testEd25519ANeg1Math(string $public, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        $math = new TwED_ANeg1_Math(BernsteinCurveFactory::edwards25519());

        $this->verifyEd25519Signature($math, $public, $signature, $message, $result);
    }

    private function verifyEd25519Signature(MathInterface $math, string $public, string $signature, string $message, string $result): void
    {
        $signer = new EdDSASignerEd25519($math);
        $verified = $signer->verify($public, $signature, $message);
        if ($verified) {
            $this->assertEquals($result, WycheProofConstants::RESULT_VALID);
        } else {
            $this->assertNotEquals($result, WycheProofConstants::RESULT_VALID);
        }
    }

    public static function provideEd448(): array
    {
        return FixturesRepository::createEddsaFixtures('ed448');
    }

    /**
     * @dataProvider provideEd448
     */
    public function testEd448UnsafeMath(string $public, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        $math = new EDUnsafeMath(BernsteinCurveFactory::edwards448());

        $this->verifyEd448Signature($math, $public, $signature, $message, $result);
    }

    /**
     * @dataProvider provideEd448
     */
    public function testEd448Math(string $public, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        $math = new EDMath(BernsteinCurveFactory::edwards448());

        $this->verifyEd448Signature($math, $public, $signature, $message, $result);
    }

    private function verifyEd448Signature(MathInterface $math, string $public, string $signature, string $message, string $result): void
    {
        $signer = new EDDSASignerEd448($math);
        $verified = $signer->verify($public, $signature, $message);
        if ($verified) {
            $this->assertEquals($result, WycheProofConstants::RESULT_VALID);
        } else {
            $this->assertNotEquals($result, WycheProofConstants::RESULT_VALID);
        }
    }
}
