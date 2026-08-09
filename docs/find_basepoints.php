<?php

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;

require __DIR__ . '/../vendor/autoload.php';

$curve = BernsteinCurveFactory::edwards25519();
$decoder = new TwEDPointDecoder($curve);
$math = new TwED_ANeg1_Math($curve);

$knownBasepoints = [];
while (true) {
    $randomPoint = bin2hex(random_bytes(32));
    $deserializer = new RFC7784Decoder();
    try {
        $uCoordinate = $deserializer->decodeUCoordinate($randomPoint, 255);
        $point = $decoder->fromXCoordinate($uCoordinate, true);
        $basepoint = $math->mul($point, $curve->getN());

        $basepointKnown = false;
        foreach ($knownBasepoints as $knownBasepoint) {
            if ($basepoint->equals($knownBasepoint)) {
                $basepointKnown = true;
                break;
            }
        }

        if (!$basepointKnown) {
            $knownBasepoints[] = $basepoint;
            print(count($knownBasepoints) . ": (" . gmp_strval($basepoint->x, 16) . "," . gmp_strval($basepoint->y, 16) . ")\n");

            if (gmp_cmp(count($knownBasepoints), $curve->getH()) === 0) {
                print("All basepoints found.");
                break;
            }
        }
    } catch (\Exception $ex) {
    }
}
