<?php

namespace Famoser\Elliptic\Tests\Serializer;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use Famoser\Elliptic\Serializer\SEC\SECEncoding;
use Famoser\Elliptic\Serializer\SECSerializer;
use PHPUnit\Framework\TestCase;

class SECSerializerTest extends TestCase
{
    public function testCompressedPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new SWPointDecoder($curve);
        $serializer = new SECSerializer($curve, $decoder, SECEncoding::COMPRESSED);

        $expectedPoint = $curve->getG();
        $serializedPoint = $serializer->serialize($expectedPoint);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testUncompressedPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new SWPointDecoder($curve);
        $serializer = new SECSerializer($curve, $decoder, SECEncoding::UNCOMPRESSED);

        $expectedPoint = $curve->getG();
        $serializedPoint = $serializer->serialize($expectedPoint);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testInfinityPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new SWPointDecoder($curve);
        $serializer = new SECSerializer($curve, $decoder);

        $infinity = Point::createInfinity();
        $serializedPoint = $serializer->serialize($infinity);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertTrue($actualPoint->equals($infinity));
    }
}
