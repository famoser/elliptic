<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Math\UnsafePrimeCurveMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class EcdsaSepkTest extends TestCase
{
    public static function provideSecp192r1(): array
    {
        return FixturesRepository::createEcdsaSha256Fixtures('secp192r1');
    }

    /**
     * @dataProvider provideSecp192r1
     */
    public function testSecp192r1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        $this->testCurve($curve, $publicKey, $message, $signature, $comment, $result, $flags);
    }

    public static function provideSecp192k1(): array
    {
        return FixturesRepository::createEcdsaSha256Fixtures('secp192k1');
    }

    /**
     * @dataProvider provideSecp192k1
     */
    public function testSecp192k1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        $this->testCurve($curve, $publicKey, $message, $signature, $comment, $result, $flags);
    }

    protected function testCurve(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, string $result, array $flags): void
    {
        // verify signature
        $unsafeMath = new UnsafePrimeCurveMath($curve);
        $signer = new ECDSASigner($unsafeMath, 'sha256');

        $verified = $signer->verify($publicKey, $signature, $message);
        if ($verified) {
            $this->assertEquals($result, WycheProofConstants::RESULT_VALID);
        } else {
            $this->assertNotEquals($result, WycheProofConstants::RESULT_VALID);
        }
    }
}
