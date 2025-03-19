<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\Rooterberg;

use Mdanter\Ecc\Integration\Utils\Signature\ECDSASigner;
use Mdanter\Ecc\Integration\Utils\Signature\Signature;
use Mdanter\Ecc\Math\UnsafeMath;
use Mdanter\Ecc\Primitives\Curve;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Serializer\PointDecoderException;
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
        // crude signature validity check, as this is not our prime concern here
        $integerOctetLength = (int) ceil((float) strlen(gmp_strval($curve->getN(), 2)) / 8);
        if (strlen($signature) !== $integerOctetLength*4) {
            $this->assertFalse($valid);
            return;
        }

        // unserialize signature
        $r = gmp_init(substr($signature, 0, $integerOctetLength*2), 16);
        $s = gmp_init(substr($signature, $integerOctetLength*2), 16);
        $signature = new Signature($r, $s);

        // verify signature
        $math = new UnsafeMath($curve);
        $signer = new ECDSASigner($math, 'sha224');
        $verified = $signer->verify($publicKey, $signature, $message);

        // check congruent with proof expectation
        if ($verified) {
            $this->assertTrue($valid);
        } else {
            $this->assertFalse($valid);
        }
    }
}
