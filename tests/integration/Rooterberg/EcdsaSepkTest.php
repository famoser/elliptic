<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\Rooterberg;

use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Curves\SecpCurves;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Point\PointSize;
use PHPUnit\Framework\TestCase;

class EcdsaSepkTest extends TestCase
{
    public static function provideSecp224k1(): array
    {
        return FixturesRepository::createEcdsaSha224Fixtures('secp224k1');
    }

    /**
     * @dataProvider provideSecp224k1
     */
    public function testSecp224k1(GeneratorPoint $generator, PublicKeyInterface $publicKey, \GMP $hash, string $signature, string $comment, bool $valid, array $flags): void
    {
        $generator = SecpCurves::create()->generator224k1();

        $this->testCurve($generator, $publicKey, $hash, $signature, $comment, $valid, $flags);
    }

    protected function testCurve(GeneratorPoint $generator, PublicKeyInterface $publicKey, \GMP $hash, string $signature, string $comment, bool $valid, array $flags): void
    {
        // crude signature decoding, as this is not our prime concern here
        $integerHexLength = strlen($signature) / 2;

        // unserialize signature
        $r = gmp_init(substr($signature, 0, $integerHexLength), 16);
        $s = gmp_init(substr($signature, $integerHexLength), 16);
        $signature = new Signature($r, $s);

        // verify signature
        $signer = new Signer(new GmpMath());
        $verified = $signer->verify($publicKey, $signature, $hash);

        // check congruent with proof expectation
        if ($verified) {
            $this->assertTrue($valid);
        } else {
            $this->assertFalse($valid);
        }
    }
}
