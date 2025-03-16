<?php

namespace Mdanter\Ecc\Integration\WycheProof;

use Mdanter\Ecc\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Exception\SquareRootException;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\Point\ChainedPointSerializer;
use Mdanter\Ecc\Serializer\Point\PointDecodingException;
use PHPUnit\Framework\TestCase;

class AbstractEcdhTest extends TestCase
{
    protected const POINT_NOT_ON_CURVE_COMMENT_WHITELIST = [
        'public point not on curve',
        'point is not on curve',
        'public point = (0,0)'
    ];

    protected const POINT_RECOVERY_JACOBI_COMMENT_WHITELIST = [
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
