<?php

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Math\UnsafePrimeCurveMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Serializer\PointDecoderException;
use Famoser\Elliptic\Serializer\PointSerializer;
use Famoser\Elliptic\Serializer\PointSerializerException;
use PHPUnit\Framework\TestCase;

abstract class AbstractEcdhTestCase extends TestCase
{
    protected const POINT_NOT_ON_CURVE_COMMENT_WHITELIST = [
        'public point not on curve',
        'point is not on curve',
        'public point = (0,0)',
        'invalid public key',
        'public key is a low order point on twist'
    ];

    protected function testCurve(Curve $curve, string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        // unserialize public key
        try {
            $pointSerializer = new PointSerializer($curve);
            $publicKey = $pointSerializer->deserialize($public);
        } catch (PointDecoderException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            if (in_array($comment, self::POINT_NOT_ON_CURVE_COMMENT_WHITELIST, true)) {
                return;
            }

            $this->fail('Test data considers other error: ' . $comment);
        } catch (PointSerializerException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            if ($public === '' || in_array('InvalidPublic', $flags)) {
                return;
            }

            $this->fail('Test data considers other error: ' . $comment);
        }

        // do DH
        $math = new UnsafePrimeCurveMath($curve);
        $sharedSecret = $math->mul($publicKey, gmp_init($private, 16));

        // check shared secret as expected
        $expectedSharedSecret = gmp_init($shared, 16);
        $this->assertEquals($expectedSharedSecret, $sharedSecret->x);

        // check congruent with Wyche proof expectation
        $this->assertNotEquals($result, WycheProofConstants::RESULT_INVALID);
    }
}
