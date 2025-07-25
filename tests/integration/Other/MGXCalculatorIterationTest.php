<?php

namespace Famoser\Elliptic\Integration\Other;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use PHPUnit\Framework\TestCase;

class MGXCalculatorIterationTest extends TestCase
{
    public function testIteration25519(): void
    {
        // https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 X25519 iteration test
        $startValue = '0900000000000000000000000000000000000000000000000000000000000000';
        $oneIteration = '422c8e7a6227d7bca1350b3e2bb7279f7897b87bb6854b783c60e80311ae3079';
        $thousandIteration = '684cf59ba83309552800ef566f2f4d3c1c3887c49360e3875f2eb94d99532c51';
        $millionIteration = '7c3911e0ab2586fd864497297e575e6f3bc601c0883c30df5f4dd2d24f665424';

        $curve = BernsteinCurveFactory::curve25519();
        $calculator = new MGXCalculator($curve);
        $decoder = new RFC7784Decoder();

        $i = 0;
        $scalar = $startValue;
        $u = $startValue;
        while ($i++ < 1000) {
            $scalarDecoded = $decoder->decodeScalar25519($scalar);
            $uDecoded = $decoder->decodeUCoordinate($u, 255);

            $result = $calculator->mul($uDecoded, $scalarDecoded);
            $encodedResult = $decoder->encodeUCoordinate($result, 255);
            if ($i === 1) {
                $this->assertEquals($oneIteration, $encodedResult);
            }

            if ($i === 1000) {
                $this->assertEquals($thousandIteration, $encodedResult);
            }

            $u = $scalar;
            $scalar = $encodedResult;
        }
    }

    public function testIteration448(): void
    {
        // https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 x448 iteration test
        $startValue = '0500000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000';
        $oneIteration = '3f482c8a9f19b01e6c46ee9711d9dc14fd4bf67af30765c2ae2b846a4d23a8cd0db897086239492caf350b51f833868b9bc2b3bca9cf4113';
        $thousandIteration = 'aa3b4749d55b9daf1e5b00288826c467274ce3ebbdd5c17b975e09d4af6c67cf10d087202db88286e2b79fceea3ec353ef54faa26e219f38';
        $millionIteration = '077f453681caca3693198420bbe515cae0002472519b3e67661a7e89cab94695c8f4bcd66e61b9b9c946da8d524de3d69bd9d9d66b997e37';

        $curve = BernsteinCurveFactory::curve448();
        $calculator = new MGXCalculator($curve);
        $decoder = new RFC7784Decoder();

        $i = 0;
        $scalar = $startValue;
        $u = $startValue;
        while ($i++ < 1000) {
            $scalarDecoded = $decoder->decodeScalar448($scalar);
            $uDecoded = $decoder->decodeUCoordinate($u, 448);

            $result = $calculator->mul($uDecoded, $scalarDecoded);
            $encodedResult = $decoder->encodeUCoordinate($result, 448);
            if ($i === 1) {
                $this->assertEquals($oneIteration, $encodedResult);
            }

            if ($i === 1000) {
                $this->assertEquals($thousandIteration, $encodedResult);
            }

            $u = $scalar;
            $scalar = $encodedResult;
        }
    }
}
