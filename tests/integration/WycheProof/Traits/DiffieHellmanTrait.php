<?php

namespace Famoser\Elliptic\Integration\WycheProof\Traits;

use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoderException;
use Famoser\Elliptic\Serializer\PointSerializer;
use Famoser\Elliptic\Serializer\PointSerializerException;

trait DiffieHellmanTrait
{
    protected function assertDHCorrect(MathInterface $math, Point $publicKey, string $private, string $shared, string $result): void
    {
        // do DH
        $sharedSecret = $math->mul($publicKey, gmp_init($private, 16));

        // check shared secret as expected
        $expectedSharedSecret = gmp_init($shared, 16);
        $this->assertEquals($expectedSharedSecret, $sharedSecret->x);

        // check congruent with Wyche proof expectation
        $this->assertNotEquals($result, WycheProofConstants::RESULT_INVALID);
    }
}
