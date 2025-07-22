<?php

namespace Famoser\Elliptic\Tests\Math\Calculator;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use PHPUnit\Framework\TestCase;

class MGXCalculatorTest extends TestCase
{
    use RFC7784TestVectorsTrait;

    /**
     * @dataProvider vectors25519
     */
    public function testTestVectors25519(string $scalar, string $u, string $expectedResult): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $calculator = new MGXCalculator($curve);

        $decoder = new RFC7784Decoder();
        $scalar = $decoder->decodeScalar25519($scalar);
        $u = $decoder->decodeUCoordinate($u, 255);

        $result = $calculator->mul($u, $scalar);
        $encodedResult = $decoder->encodeUCoordinate($result, 255);
        $this->assertEquals($expectedResult, $encodedResult);
    }

    /**
     * @dataProvider vectors448
     */
    public function testTestVectors448(string $scalar, string $u, string $expectedResult): void
    {
        $curve = BernsteinCurveFactory::curve448();
        $calculator = new MGXCalculator($curve);

        $decoder = new RFC7784Decoder();
        $scalar = $decoder->decodeScalar448($scalar);
        $u = $decoder->decodeUCoordinate($u, 448);

        $result = $calculator->mul($u, $scalar);
        $encodedResult = $decoder->encodeUCoordinate($result, 448);
        $this->assertEquals($expectedResult, $encodedResult);
    }

    public function testIteration25519(): void
    {
        // https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 X25519 iteration test
        $startValue = '0900000000000000000000000000000000000000000000000000000000000000';
        $oneIteration = '422c8e7a6227d7bca1350b3e2bb7279f7897b87bb6854b783c60e80311ae3079';
        // other iterations are tested as part of integration tests

        $curve = BernsteinCurveFactory::curve25519();
        $calculator = new MGXCalculator($curve);
        $decoder = new RFC7784Decoder();

        $scalar = $decoder->decodeScalar25519($startValue);
        $u = $decoder->decodeUCoordinate($startValue, 255);

        $result = $calculator->mul($u, $scalar);
        $encodedResult = $decoder->encodeUCoordinate($result, 255);
        $this->assertEquals($oneIteration, $encodedResult);
    }

    public function testIteration448(): void
    {
        // https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 X448 iteration test
        $startValue = '0500000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000';
        $oneIteration = '3f482c8a9f19b01e6c46ee9711d9dc14fd4bf67af30765c2ae2b846a4d23a8cd0db897086239492caf350b51f833868b9bc2b3bca9cf4113';

        $curve = BernsteinCurveFactory::curve448();
        $calculator = new MGXCalculator($curve);

        $decoder = new RFC7784Decoder();
        $scalar = $decoder->decodeScalar448($startValue);
        $u = $decoder->decodeUCoordinate($startValue, 448);

        $result = $calculator->mul($u, $scalar);
        $encodedResult = $decoder->encodeUCoordinate($result, 448);
        $this->assertEquals($oneIteration, $encodedResult);
    }
}
