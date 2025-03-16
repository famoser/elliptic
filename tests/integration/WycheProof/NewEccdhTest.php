<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\WycheProof;

use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Exception\ExchangeException;
use Mdanter\Ecc\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Exception\SquareRootException;
use Mdanter\Ecc\Integration\Utils\DER\UnsafeDerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Point\PointDecodingException;
use function PHPUnit\Framework\assertEquals;

class NewEccdhTest extends AbstractTestCase
{
    private function readTestvectors(string $curve): array
    {
        $path = __DIR__ . "/fixtures/testvectors/ecdh_{$curve}_test.json";
        $testvectorsJson = file_get_contents($path);
        if (!$testvectorsJson) {
            throw new \InvalidArgumentException("Failed to read test fixture file $path");
        }

        return json_decode($testvectorsJson, true);
    }

    private function createFilteredFixtures(array $testvectors): array
    {
        $results = [];

        // if untrue, check that tcIds do not override each other
        assert(1 === count($testvectors['testGroups']));

        foreach ($testvectors['testGroups'] as $testvectorsGroup) {
            foreach ($testvectorsGroup['tests'] as $testvector) {
                $tcId = "tcId: " . $testvector['tcId'];

                // skip testing the ASN library
                if (in_array(WycheProofConstants::FLAG_INVALID_ASN, $testvector['flags'], true)) {
                    continue;
                }

                // skip unnamed curves DER (as the DER encoding is just used for testing, not exposed outside)
                if (in_array(WycheProofConstants::FLAG_UNNAMED_CURVE, $testvector['flags'], true)) {
                    continue;
                }

                // skip wrong curves
                if (str_starts_with($testvector['comment'], 'Public key uses wrong curve:')) {
                    continue;
                }

                $results[$tcId] = [
                    $testvector['comment'],
                    $testvector['public'],
                    $testvector['private'],
                    $testvector['shared'],
                    $testvector['result'],
                    $testvector['flags'] ?? [],
                ];
            }
        }

        return $results;
    }

    public function getSecp256r1Fixtures(): array
    {
        $testvectors = $this->readTestvectors("secp256r1");
        return $this->createFilteredFixtures($testvectors);
    }

    /**
     * @dataProvider getSecp256r1Fixtures
     */
    public function testSecp256r1(string $comment, string $public, string $private, string $shared, string $result, array $flags)
    {
        return $this->testCurve("secp256r1", $comment, $public, $private, $shared, $result, $flags);
    }

    private const POINT_NOT_ON_CURVE_COMMENT_WHITELIST = [
        'point is not on curve',
        'public key has invalid point of order 2 on secp256k1.  The point of the public key is a valid on secp256r1.',
        'public point not on curve',
        'public point = (0,0)'
    ];

    private const POINT_RECOVERY_JACOBI_COMMENT_WHITELIST = [
        'invalid public key',
        'public key is a low order point on twist'
    ];

    protected function testCurve(string $expectedCurve, string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // unserialize public key
        try {
            $pubKeySerializer = UnsafeDerPublicKeySerializer::create();
            $publicKey = $pubKeySerializer->parse(hex2bin($public));
        } catch (PointNotOnCurveException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            if (in_array($comment, self::POINT_NOT_ON_CURVE_COMMENT_WHITELIST, true)) {
                return;
            }

            $this->fail('Test data considers other error: ' . $comment);
        } catch (PointRecoveryException $ex) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            $jacobiException = $ex->getPrevious() instanceof SquareRootException && $ex->getPrevious()->getCode() === SquareRootException::CODE_JACOBI;
            if (in_array($comment, self::POINT_RECOVERY_JACOBI_COMMENT_WHITELIST, true) && $jacobiException) {
                return;
            }

            $this->fail('Test data considers other error: ' . $comment);
        } catch (PointDecodingException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);

            // concerns tcId: 210; unfortunately comment/flags are empty, hence no other way to whitelist except using the public key directly
            if ($public === '3018301306072a8648ce3d020106082a8648ce3d030107030100') {
                return;
            }

            $this->fail('Test data considers other error: ' . $comment);
        }

        $curve = $publicKey->getCurve();

        // some tests use a public key on an invalid curve; filtered out here
        if ($curve instanceof NamedCurveFp && $curve->getName() !== $expectedCurve) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            return;
        }

        // unserialize private key
        $generator = $publicKey->getGenerator();
        $privateKey = $generator->getPrivateKeyFrom(gmp_init($private, 16));

        // do DH
        $dh = $privateKey->createExchange($publicKey);
        $sharedSecret = $dh->calculateSharedKey();

        $expectedSharedSecret = gmp_init($shared, 16);

        $this->assertEquals($expectedSharedSecret, $sharedSecret);
        $this->assertNotEquals($result, WycheProofConstants::RESULT_INVALID);
    }
}
