<?php

namespace Famoser\Elliptic\Serializer;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * TODO implement https://www.secg.org/SEC1-Ver-1.0.pdf
 */
class PointSerializer
{
    private readonly int $pointOctetLength;
    private readonly PointDecoder $pointDecoder;

    public function __construct(private readonly Curve $curve, private readonly PointEncoding $preferredEncoding = PointEncoding::ENCODING_COMPRESSED)
    {
        $this->pointOctetLength = (int) ceil((float) strlen(gmp_strval($this->curve->getP(), 2)) / 8);
        $this->pointDecoder = new PointDecoder($this->curve);
    }

    /**
     * implements https://www.secg.org/SEC1-Ver-1.0.pdf 2.3.4
     *
     * @throws PointDecoderException|PointSerializerException
     */
    public function deserialize(string $hex): Point
    {
        if ($hex === '00') {
            return Point::createInfinity();
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

        throw new PointSerializerException('Unknown deserialization format.');
    }

    /**
     * implements https://www.secg.org/SEC1-Ver-1.0.pdf 2.3.3
     */
    public function serialize(Point $point): string
    {
        if ($point->isInfinity()) {
            return '00';
        }

        if ($this->preferredEncoding === PointEncoding::ENCODING_COMPRESSED) {
            $x = str_pad(gmp_strval($point->x, 16), $this->pointOctetLength * 2, '0', STR_PAD_LEFT);

            $isEven = gmp_cmp(gmp_mod($point->y, 2), 0);
            $prefix = $isEven ? '02' : '03';

            return $prefix . $x;
        }

        if ($this->preferredEncoding === PointEncoding::ENCODING_UNCOMPRESSED) {
            $x = str_pad(gmp_strval($point->x, 16), $this->pointOctetLength * 2, '0', STR_PAD_LEFT);
            $y = str_pad(gmp_strval($point->y, 16), $this->pointOctetLength * 2, '0', STR_PAD_LEFT);

            return '04' . $x . $y;
        }

        throw new PointSerializerException('Unknown serialization format.');
    }
}
