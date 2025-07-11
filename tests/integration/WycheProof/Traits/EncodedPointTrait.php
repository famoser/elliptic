<?php

namespace Famoser\Elliptic\Integration\WycheProof\Traits;

use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoderException;
use Famoser\Elliptic\Serializer\PointSerializer;
use Famoser\Elliptic\Serializer\PointSerializerException;

trait EncodedPointTrait
{
    protected function assertPublicKeyPointDecodes(MathInterface $math, string $comment, string $public, string $result, array $flags, Point &$publicKey = null): void
    {
        // unserialize public key
        try {
            $pointSerializer = new PointSerializer($math->getCurve());
            $publicKey = $pointSerializer->deserialize($public);
        } catch (PointDecoderException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            if (in_array($comment, WycheProofConstants::POINT_DECODING_FAIL_COMMENT_WHITELIST, true)) {
                $this->markTestSkipped('Decoding failed (as expected)');
            }

            $this->fail('Test data considers other error: ' . $comment);
        } catch (PointSerializerException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            if ($public === '' || in_array('InvalidPublic', $flags)) {
                $this->markTestSkipped('Serialization failed (as expected)');
            }

            $this->fail('Test data considers other error: ' . $comment);
        }
    }
}
