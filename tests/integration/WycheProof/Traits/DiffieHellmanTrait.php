<?php

namespace Famoser\Elliptic\Integration\WycheProof\Traits;

use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;

trait DiffieHellmanTrait
{
    protected function assertDHCorrect(MathInterface $math, Point $publicKey, \GMP $private, \GMP $expectedShared, string $result): void
    {
        // do DH
        $sharedSecret = $math->mul($publicKey, $private);

        // check shared secret as expected
        $this->assertEquals(0, gmp_cmp($expectedShared, $sharedSecret->x));

        // check congruent with Wyche proof expectation
        $this->assertNotEquals($result, WycheProofConstants::RESULT_INVALID);
    }
}
