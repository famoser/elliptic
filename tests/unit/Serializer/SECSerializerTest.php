<?php

namespace Famoser\Elliptic\Tests\Serializer;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use Famoser\Elliptic\Serializer\SEC\SECEncoding;
use Famoser\Elliptic\Serializer\SECSerializer;
use Famoser\Elliptic\Serializer\SerializerException;
use PHPUnit\Framework\TestCase;

class SECSerializerTest extends TestCase
{
    private static function createSerializer(Curve $curve, SECEncoding $preferredEncoding): SECSerializer
    {
        $math = new SWUnsafeMath($curve);
        $decoder = new SWPointDecoder($curve);
        return new SECSerializer($math, $decoder, $preferredEncoding);
    }

    public function testCompressedPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $serializer = self::createSerializer($curve, SECEncoding::COMPRESSED);

        $expectedPoint = $curve->getG();
        $serializedPoint = $serializer->serialize($expectedPoint);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testUncompressedPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $serializer = self::createSerializer($curve, SECEncoding::UNCOMPRESSED);

        $expectedPoint = $curve->getG();
        $serializedPoint = $serializer->serialize($expectedPoint);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testInfinityPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $math = new SWUnsafeMath($curve);
        $decoder = new SWPointDecoder($curve);
        $serializer = new SECSerializer($math, $decoder);

        $infinity = $math->getInfinity();
        $serializedPoint = $serializer->serialize($infinity);
        $actualPoint = $serializer->deserialize($serializedPoint);

        $this->assertTrue($actualPoint->equals($infinity));
    }
    
    public function testInvalidInput(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $serializer = self::createSerializer($curve, SECEncoding::UNCOMPRESSED);

        $this->expectException(SerializerException::class);;
        $serializer->deserialize('0900023');
    }
}
