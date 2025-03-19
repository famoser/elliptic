<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\WycheProof;

use Mdanter\Ecc\Integration\Utils\Key\PublicKeyInterface;
use Mdanter\Ecc\Integration\Utils\Signature\Signature;
use Mdanter\Ecc\Integration\Utils\Signature\Signer;
use Mdanter\Ecc\Legacy\Curves\SecpCurves;
use Mdanter\Ecc\Legacy\Math\GmpMath;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Mdanter\Ecc\Legacy\Serializer\Point\PointSize;
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
    public function testSecp192r1(GeneratorPoint $generator, PublicKeyInterface $publicKey, \GMP $hash, string $signature, string $comment, string $result, array $flags): void
    {
        $generator = SecpCurves::create()->generator192r1();

        $this->testCurve($generator, $publicKey, $hash, $signature, $comment, $result, $flags);
    }

    public static function provideSecp192k1(): array
    {
        return FixturesRepository::createEcdsaSha256Fixtures('secp192k1');
    }

    /**
     * @dataProvider provideSecp192k1
     */
    public function testSecp192k1(GeneratorPoint $generator, PublicKeyInterface $publicKey, \GMP $hash, string $signature, string $comment, string $result, array $flags): void
    {
        $generator = SecpCurves::create()->generator192k1();

        $this->testCurve($generator, $publicKey, $hash, $signature, $comment, $result, $flags);
    }

    protected function testCurve(GeneratorPoint $generator, PublicKeyInterface $publicKey, \GMP $hash, string $signature, string $comment, string $result, array $flags): void
    {
        // crude signature validity check, as this is not our prime concern here
        $integerHexLength = PointSize::getByteSize($generator->getCurve()) * 2;
        if (strlen($signature) !== $integerHexLength*2) {
            $this->assertNotEquals($result, WycheProofConstants::RESULT_VALID);
            return;
        }

        // unserialize signature
        $r = gmp_init(substr($signature, 0, $integerHexLength), 16);
        $s = gmp_init(substr($signature, $integerHexLength), 16);
        $signature = new Signature($r, $s);

        // verify signature
        $signer = new Signer(new GmpMath());
        $verified = $signer->verify($publicKey, $signature, $hash);

        // check congruent with Wyche proof expectation
        if ($verified) {
            $this->assertEquals($result, WycheProofConstants::RESULT_VALID);
        } else {
            $this->assertNotEquals($result, WycheProofConstants::RESULT_VALID);
        }
    }
}
