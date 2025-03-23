<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\Rooterberg;

use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Math\UnsafePrimeCurveMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoderException;
use PHPUnit\Framework\TestCase;

class EcdsaSepkTest extends TestCase
{
    /**
     * @throws PointDecoderException
     */
    public static function provideSecp224k1(): array
    {
        return FixturesRepository::createEcdsaSha224Fixtures('secp224k1');
    }

    /**
     * @dataProvider provideSecp224k1
     */
    public function testSecp224k1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $this->testCurve($curve, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException
     */
    public static function provideSecp224r1(): array
    {
        return FixturesRepository::createEcdsaSha224Fixtures('secp224r1');
    }

    /**
     * @dataProvider provideSecp224r1
     */
    public function testSecp224r1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $this->testCurve($curve, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    protected function testCurve(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new UnsafePrimeCurveMath($curve);
        $signer = new ECDSASigner($math, 'sha224');

        $verified = $signer->verify($publicKey, $signature, $message);
        if ($verified) {
            $this->assertTrue($valid);
        } else {
            $this->assertFalse($valid);
        }
    }
}
