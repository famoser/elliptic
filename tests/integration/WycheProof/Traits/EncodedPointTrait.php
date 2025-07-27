<?php

namespace Famoser\Elliptic\Integration\WycheProof\Traits;

use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use Famoser\Elliptic\Serializer\SECSerializer;
use Famoser\Elliptic\Serializer\SerializerException;

trait EncodedPointTrait
{
    /**
     * @param-out Point $publicKey
     */
    protected function assertSWPublicKeyDeserializes(MathInterface $math, string $comment, string $public, string $result, array $flags, ?Point &$publicKey = null): void
    {
        // unserialize public key
        try {
            $pointSerializer = new SECSerializer($math, new SWPointDecoder($math->getCurve()));
            $publicKey = $pointSerializer->deserialize($public);
        } catch (PointDecoderException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            if (in_array($comment, WycheProofConstants::POINT_DECODING_FAIL_COMMENT_WHITELIST, true)) {
                $this->markTestSkipped('Decoding failed (as expected)');
            }

            $this->fail('Test data considers other error: ' . $comment);
        } catch (SerializerException) {
            $this->assertEquals($result, WycheProofConstants::RESULT_INVALID);
            if ($public === '' || in_array('InvalidPublic', $flags)) {
                $this->markTestSkipped('Serialization failed (as expected)');
            }

            $this->fail('Test data considers other error: ' . $comment);
        }
    }
}
