<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\WycheProof;

use Mdanter\Ecc\Curves\SEC2CurveFactory;
use Mdanter\Ecc\Legacy\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Primitives\Curve;
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
        if (str_contains($comment, "The point of the public key is a valid on secp256k1.")) {
            $result = WycheProofConstants::RESULT_VALID;
        }

        if (str_contains($comment, 'using secp224r1') || str_contains($comment, 'using secp256r1')) {
            $comment = parent::POINT_NOT_ON_CURVE_COMMENT_WHITELIST[0];
        }

        $curve = SEC2CurveFactory::secp256k1();
        $this->testCurve($curve, $comment, $public, $private, $shared, $result, $flags);
    }

    protected function testCurve(Curve $curve, string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // unserialize public key from DER format
        $asnObject = UnspecifiedType::fromDER(hex2bin($public));
        $encodedKey = $asnObject->asSequence()->at(1)->asBitString();
        $publicKey = bin2hex($encodedKey->string());

        parent::testCurve($curve, $comment, $publicKey, $private, $shared, $result, $flags);
    }
}
