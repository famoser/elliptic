<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\Utils;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\Decoder\BinaryDecoder;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;

/**
 * implements Ed25519 according to RFC8032
 *
 * notably follows closely https://datatracker.ietf.org/doc/html/rfc8032#section-6
 */
class EDDSASigner
{
    public function __construct(private readonly MathInterface $math)
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
        if (strlen($publicKeyHex) !== 64) {
            throw new \RuntimeException("Bad public key length");
        }
        if (strlen($signatureHex) !== 128) {
            throw new \RuntimeException("Bad signature length");
        }

        $A = $this->decompressPoint($publicKeyHex);
        $Rs = substr($signatureHex, 0, 64);
        $R = $this->decompressPoint($Rs);

        $ss = substr($signatureHex, 64);
        $s = $this->decodeScalar($ss);

        $h = $this->hashToScalar($Rs . $publicKeyHex . $messageHex);

        $sB = $this->math->mulG($s);
        $hA = $this->math->mul($A, $h);

        return $sB->equals($this->math->add($R, $hA));
    }

    private function decompressPoint(string $pointHex): Point
    {
        $decoder = new RFC7784Decoder();
        $y = $decoder->decodeUCoordinate($pointHex, 255);

        $signByte = substr($pointHex, strlen($pointHex) - 2);
        $isEven = !((hexdec($signByte) >> 7 & 1));

        $decoder = new TwEDPointDecoder($this->math->getCurve());
        return $decoder->fromYCoordinate($y, $isEven);
    }

    private function decodeScalar(string $ss): \GMP
    {
        $decoder = new BinaryDecoder();
        $b = $decoder->decodeHexToByteArray($ss);
        $s = $decoder->decodeLittleEndian($b, 256);

        if ($s >= $this->math->getCurve()->getN()) {
            throw new \RuntimeException("s > N; abort");
        }

        return $s;
    }

    private function hashToScalar(string $hex): \GMP
    {
        $content = hex2bin($hex);
        $digestHex = hash('sha512', $content);

        $decoder = new BinaryDecoder();
        $b = $decoder->decodeHexToByteArray($digestHex);
        return $decoder->decodeLittleEndian($b, 512);
    }
}
