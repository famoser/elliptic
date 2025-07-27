<?php

namespace Famoser\Elliptic\Serializer;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Serializer\SEC\SECEncoding;
use Famoser\Elliptic\Serializer\SEC\SECPointDecoderInterface;

/**
 * implements serialization as described in https://www.secg.org/SEC1-Ver-1.0.pdf
 */
class SECSerializer
{
    private readonly int $pointOctetLength;

    public function __construct(private readonly MathInterface $math, private readonly SECPointDecoderInterface $pointDecoder, private readonly SECEncoding $preferredEncoding = SECEncoding::COMPRESSED)
    {
        $this->pointOctetLength = (int) ceil((float) strlen(gmp_strval($this->math->getCurve()->getP(), 2)) / 8);
    }

    /**
     * implements https://wdecoderww.secg.org/SEC1-Ver-1.0.pdf 2.3.4
     *
     * @throws PointDecoderException|SerializerException
     */
    public function deserialize(string $hex): Point
    {
        if ($hex === '00') {
            return $this->math->getInfinity();
        }

        $compressedFormatOctetLength = 1 + $this->pointOctetLength;
        if (strlen($hex) === 2 * $compressedFormatOctetLength && (str_starts_with($hex, '02') || str_starts_with($hex, '03'))) {
            $x = gmp_init(substr($hex, 2), 16);
            $isEvenY = str_starts_with($hex, '02');

            return $this->pointDecoder->fromXCoordinate($x, $isEvenY);
        }

        $uncompressedFormatOctetLength = 1 + 2 * $this->pointOctetLength;
        if (strlen($hex) === 2 * $uncompressedFormatOctetLength && str_starts_with($hex, '04')) {
            $pointHexLength = 2 * $this->pointOctetLength;
            $x = gmp_init(substr($hex, 2, $pointHexLength), 16);
            $y = gmp_init(substr($hex, 2 + $pointHexLength), 16);

            return $this->pointDecoder->fromCoordinates($x, $y);
        }

        throw new SerializerException('Unknown deserialization format.');
    }

    /**
     * implements https://www.secg.org/SEC1-Ver-1.0.pdf 2.3.3
     */
    public function serialize(Point $point): string
    {
        if ($this->math->isInfinity($point)) {
            return '00';
        }

        return match ($this->preferredEncoding) {
            SECEncoding::COMPRESSED => $this->serializeCompressed($point),
            SECEncoding::UNCOMPRESSED => $this->serializeUncompressed($point)
        };
    }

    private function serializeCompressed(Point $point): string
    {
        $x = str_pad(gmp_strval($point->x, 16), $this->pointOctetLength * 2, '0', STR_PAD_LEFT);

        $isEven = gmp_cmp(gmp_mod($point->y, 2), 0) === 0;
        $prefix = $isEven ? '02' : '03';

        return $prefix . $x;
    }

    private function serializeUncompressed(Point $point): string
    {
        $x = str_pad(gmp_strval($point->x, 16), $this->pointOctetLength * 2, '0', STR_PAD_LEFT);
        $y = str_pad(gmp_strval($point->y, 16), $this->pointOctetLength * 2, '0', STR_PAD_LEFT);

        return '04' . $x . $y;
    }
}
