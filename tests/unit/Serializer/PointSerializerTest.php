<?php

namespace Famoser\Elliptic\Tests\Serializer;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder;
use Famoser\Elliptic\Serializer\PointDecoderException;
use Famoser\Elliptic\Serializer\PointEncoding;
use Famoser\Elliptic\Serializer\PointSerializer;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class PointSerializerTest extends TestCase
{
    public function testCompressedPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $serializer = new PointSerializer($curve, PointEncoding::ENCODING_COMPRESSED);

        $expectedPoint = $curve->getG();
        $serializedPoint = $serializer->serialize($expectedPoint);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertEquals($expectedPoint, $actualPoint);
    }

    public function testUncompressedPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $serializer = new PointSerializer($curve, PointEncoding::ENCODING_UNCOMPRESSED);

        $expectedPoint = $curve->getG();
        $serializedPoint = $serializer->serialize($expectedPoint);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertEquals($expectedPoint, $actualPoint);
    }

    public function testInfinityPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $serializer = new PointSerializer($curve);

        $infinity = Point::createInfinity();
        $serializedPoint = $serializer->serialize($infinity);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertEquals($infinity, $actualPoint);
    }
}
