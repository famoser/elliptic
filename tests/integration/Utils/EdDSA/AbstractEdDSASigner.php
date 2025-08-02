<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Tests\Integration\Utils\EdDSA;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\Decoder\BinaryDecoder;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointYDecoderInterface;

/**
 * implements EdDSA according to RFC8032
 *
 * notably follows closely https://datatracker.ietf.org/doc/html/rfc8032#section-6
 */
abstract class AbstractEdDSASigner
{
    public function __construct(private readonly MathInterface $math, private readonly PointYDecoderInterface $decoder, private readonly int $byteSize)
    {
    }

    public function verify(string $publicKeyHex, string $signatureHex, string $messageHex): bool
    {
        try {
            return $this->verifyInternal($publicKeyHex, $signatureHex, $messageHex);
        } catch (\Exception) {
            return false;
        }
    }

    private function verifyInternal(string $publicKeyHex, string $signatureHex, string $messageHex): bool
    {
        if (strlen($publicKeyHex) !== $this->byteSize * 2) {
            throw new \RuntimeException("Bad public key length");
        }
        if (strlen($signatureHex) !== $this->byteSize * 4) {
            throw new \RuntimeException("Bad signature length");
        }

        $A = $this->decompressPoint($publicKeyHex);
        $Rs = substr($signatureHex, 0, $this->byteSize * 2);
        $R = $this->decompressPoint($Rs);

        $ss = substr($signatureHex, $this->byteSize * 2);
        $s = $this->decodeScalar($ss);

        $h = $this->hashToScalar($Rs . $publicKeyHex . $messageHex);

        $sB = $this->math->mulG($s);
        $hA = $this->math->mul($A, $h);

        return $sB->equals($this->math->add($R, $hA));
    }

    private function decompressPoint(string $pointHex): Point
    {
        $decoder = new RFC7784Decoder();
        $y = $decoder->decodeUCoordinate($pointHex, $this->byteSize * 8 - 1);

        $signByte = substr($pointHex, strlen($pointHex) - 2);
        $isEven = !((hexdec($signByte) >> 7 & 1));

        return $this->decoder->fromYCoordinate($y, $isEven);
    }

    private function decodeScalar(string $ss): \GMP
    {
        $decoder = new BinaryDecoder();
        $b = $decoder->decodeHexToByteArray($ss);
        $s = $decoder->decodeLittleEndian($b, $this->byteSize * 8);

        if ($s >= $this->math->getCurve()->getN()) {
            throw new \RuntimeException("s > N; abort");
        }

        return $s;
    }

    private function hashToScalar(string $hex): \GMP
    {
        $digestHex = $this->hash($hex);

        $decoder = new BinaryDecoder();
        $b = $decoder->decodeHexToByteArray($digestHex);
        return $decoder->decodeLittleEndian($b, $this->byteSize * 16);
    }

    abstract protected function hash(string $hex): string;
}
