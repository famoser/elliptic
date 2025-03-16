<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\WycheProof;

use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\Exception\ExchangeException;
use Mdanter\Ecc\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Exception\SquareRootException;
use Mdanter\Ecc\Integration\Utils\DER\UnsafeDerPublicKeySerializer;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Point\ChainedPointSerializer;
use Mdanter\Ecc\Serializer\Point\PointDecodingException;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class EcdhSepkEcpointTest extends TestCase
{
    private function getFixtures(string $testcase): array
    {
        $curve = str_replace('testSecp', 'secp', $testcase);
        return FixturesRepository::createEcdhEcpointFixtures($curve);
    }

    /**
     * @dataProvider getFixtures
     */
    public function testSecp224r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $this->markTestSkipped();
    }

    /**
     * @dataProvider getFixtures
     */
    public function testSecp256r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $generator = SecgCurve::create()->generator256r1();
        $this->testCurve($generator, $comment, $public, $private, $shared, $result, $flags);
    }

    /**
     * @dataProvider getFixtures
     */
    public function testSecp384r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $generator = SecgCurve::create()->generator384r1();
        $this->testCurve($generator, $comment, $public, $private, $shared, $result, $flags);
    }

    /**
     * @dataProvider getFixtures
     */
    public function testSecp521r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $this->markTestSkipped();
    }

    private const POINT_NOT_ON_CURVE_COMMENT_WHITELIST = [
        'point is not on curve',
    ];

    private const POINT_RECOVERY_JACOBI_COMMENT_WHITELIST = [
        'invalid public key',
        'public key is a low order point on twist'
    ];

    protected function testCurve(GeneratorPoint $generator, string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // unserialize public key
        try {
            $pointSerializer = ChainedPointSerializer::create();
            $publicKey = $pointSerializer->deserialize($generator->getCurve(), $public);
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
            if ($public === '') {
                return;
            }

            $this->fail('Test data considers other error: ' . $comment);
        }

        // do DH
        $privateKey = $generator->mul(gmp_init($private, 16));
        $secret = $privateKey->mul($publicKey->getX());

        // check shared secret as expected
        $expectedSharedSecret = gmp_init($shared, 16);
        $this->assertEquals($expectedSharedSecret, $secret->getX());

        // check congruent with Wyche proof expectation
        $this->assertNotEquals($result, WycheProofConstants::RESULT_INVALID);
    }
}
