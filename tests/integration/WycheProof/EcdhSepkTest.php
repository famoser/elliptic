<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\WycheProof;

use Mdanter\Ecc\Integration\Utils\DER\UnsafeDerRawPublicKeySerializer;
use Mdanter\Ecc\Legacy\Curves\SecpCurves;
use Mdanter\Ecc\Legacy\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Sop\ASN1\Type\UnspecifiedType;

class EcdhSepkTest extends AbstractEcdhTestCase
{
    public static function provideSecp256k1(): array
    {
        return FixturesRepository::createFilteredEcdhFixtures('secp256k1');
    }

    /**
     * @dataProvider provideSecp256k1
     */
    public function testSecp256k1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $generator = SecpCurves::create()->generator256k1();

        if (str_contains($comment, "The point of the public key is a valid on secp256k1.")) {
            $result = WycheProofConstants::RESULT_VALID;
        }

        if (str_contains($comment, 'using secp224r1') || str_contains($comment, 'using secp256r1')) {
            $comment = parent::POINT_NOT_ON_CURVE_COMMENT_WHITELIST[0];
        }

        $this->testCurve($generator, $comment, $public, $private, $shared, $result, $flags);
    }

    protected function testCurve(GeneratorPoint $generator, string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // unserialize public key from DER format
        try {
            $asnObject = UnspecifiedType::fromDER(hex2bin($public));
            $encodedKey  = $asnObject->asSequence()->at(1)->asBitString();
            $publicKey = bin2hex($encodedKey->string());
        } catch (PointNotOnCurveException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            if ($comment === 'public point not on curve') {
                return;
            }

            $this->fail('Test data considers other error: ' . $comment);
        }

        parent::testCurve($generator, $comment, $publicKey, $private, $shared, $result, $flags);
    }
}
