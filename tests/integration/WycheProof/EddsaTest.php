<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Integration\Utils\EDDSASigner;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use PHPUnit\Framework\TestCase;

class EddsaTest extends TestCase
{
    public static function provideCurve25519(): array
    {
        return FixturesRepository::createEddsaFixtures('eddsa');
    }

    /**
     * @dataProvider provideCurve25519
     */
    public function testEd25519UnsafeMath(string $public, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        $math = new TwEDUnsafeMath(BernsteinCurveFactory::edwards25519());

        $this->verifyEd25519Signature($math, $public, $signature, $message, $result);
    }

    /**
     * @dataProvider provideCurve25519
     */
    public function testEd25519ANeg1Math(string $public, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        $math = new TwED_ANeg1_Math(BernsteinCurveFactory::edwards25519());

        $this->verifyEd25519Signature($math, $public, $signature, $message, $result);
    }

    public function verifyEd25519Signature(MathInterface $math, string $public, string $signature, string $message, string $result): void
    {
        $signer = new EDDSASigner($math);
        $verified = $signer->verify($public, $signature, $message);
        if ($verified) {
            $this->assertEquals($result, WycheProofConstants::RESULT_VALID);
        } else {
            $this->assertNotEquals($result, WycheProofConstants::RESULT_VALID);
        }
    }
}
