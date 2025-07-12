<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\Rooterberg;

use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Serializer\SerializerException;
use PHPUnit\Framework\TestCase;

class EcdsaTest extends TestCase
{
    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideSecp224k1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('secp224k1', 224);
    }

    /**
     * @dataProvider provideSecp224k1
     */
    public function testSecp224k1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SWUnsafeMath($curve);
        $this->testCurve($math, 224, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideSecp224r1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('secp224r1', 224);
    }

    /**
     * @dataProvider provideSecp224r1
     */
    public function testSecp224r1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, 224, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideBrainpoolP192r1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('brainpool_p192r1', 224);
    }

    /**
     * @dataProvider provideBrainpoolP192r1
     */
    public function testBrainpoolP192r1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SW_QT_ANeg3_Math($curve, BrainpoolCurveFactory::p192r1TwistToP192t1());
        $this->testCurve($math, 224, $publicKey, $message, $signature, $comment, $valid, $flags);
    }


    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideBrainpoolP192t1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('brainpool_p192t1', 224);
    }

    /**
     * @dataProvider provideBrainpoolP192t1
     */
    public function testBrainpoolP192t1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, 224, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideBrainpoolP224t1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('brainpool_p224t1', 224);
    }

    /**
     * @dataProvider provideBrainpoolP224t1
     */
    public function testBrainpoolP224t1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, 224, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideBrainpoolP256t1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('brainpool_p256t1', 256);
    }

    /**
     * @dataProvider provideBrainpoolP256t1
     */
    public function testBrainpoolP256t1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, 256, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideBrainpoolP320t1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('brainpool_p320t1', 384);
    }

    /**
     * @dataProvider provideBrainpoolP320t1
     */
    public function testBrainpoolP320t1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, 384, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideBrainpoolP384t1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('brainpool_p384t1', 384);
    }

    /**
     * @dataProvider provideBrainpoolP384t1
     */
    public function testBrainpoolP384t1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, 384, $publicKey, $message, $signature, $comment, $valid, $flags);
    }

    /**
     * @throws PointDecoderException|SerializerException
     */
    public static function provideBrainpoolP512t1(): array
    {
        return FixturesRepository::createSWEcdsaFixtures('brainpool_p512t1', 512);
    }

    /**
     * @dataProvider provideBrainpoolP512t1
     */
    public function testBrainpoolP512t1(Curve $curve, Point $publicKey, string $message, string $signature, string $comment, bool $valid, array $flags): void
    {
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, 512, $publicKey, $message, $signature, $comment, $valid, $flags);
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
