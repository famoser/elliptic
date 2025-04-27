<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\Rooterberg;

use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\UnsafePrimeCurveMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoderException;
use Famoser\Elliptic\Serializer\PointSerializerException;
use PHPUnit\Framework\TestCase;

class EcdsaTest extends TestCase
{
    /**
     * @throws PointDecoderException|PointSerializerException
     */
    public static function provideSecp224k1(): array
    {
        return FixturesRepository::createEcdsaFixtures('secp224k1', 224);
    }

    /**
     * @dataProvider provideSecp224k1
     */
    public function testSecp224k1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $aneg3Math = new UnsafePrimeCurveMath($curve);
        $this->testCurve($aneg3Math, 224, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException|PointSerializerException
     */
    public static function provideSecp224r1(): array
    {
        return FixturesRepository::createEcdsaFixtures('secp224r1', 224);
    }

    /**
     * @dataProvider provideSecp224r1
     */
    public function testSecp224r1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $aneg3Math = new SW_ANeg3_Math($curve);
        $this->testCurve($aneg3Math, 224, $publicKey, $message, $signature, $comment, $valid, $flags);
    }


    /**
     * @throws PointDecoderException|PointSerializerException
     */
    public static function provideBrainpoolP256t1(): array
    {
        return FixturesRepository::createEcdsaFixtures('brainpool_p256t1', 256);
    }

    /**
     * @dataProvider provideBrainpoolP256t1
     */
    public function testBrainpoolP256t1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $aneg3Math = new SW_ANeg3_Math($curve);
        $this->testCurve($aneg3Math, 256, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    protected function testCurve(MathInterface $math, int $shaSize, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $signer = new ECDSASigner($math, 'sha' . $shaSize);

        $verified = $signer->verify($publicKey, $signature, $message);
        if ($verified) {
            $this->assertTrue($valid);
        } else {
            $this->assertFalse($valid);
        }
    }
}
