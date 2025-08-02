<?php

namespace Famoser\Elliptic\Tests\Unit\Curves;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class SEC2Test extends TestCase
{
    public static function compressedGPoint(): array
    {
        // from the spec of SEC2 curves (https://www.secg.org/sec2-v2.pdf)
        return [
            [SEC2CurveFactory::secp192k1(), '04 DB4FF10E C057E9AE 26B07D02 80B7F434 1DA5D1B1 EAE06C7D
9B2F2F6D 9C5628A7 844163D0 15BE8634 4082AA88 D95E2F9D'],
            [SEC2CurveFactory::secp192r1(), '04 188DA80E B03090F6 7CBF20EB 43A18800 F4FF0AFD 82FF1012
07192B95 FFC8DA78 631011ED 6B24CDD5 73F977A1 1E794811'],
            [SEC2CurveFactory::secp224k1(), '04 A1455B33 4DF099DF 30FC28A1 69A467E9 E47075A9 0F7E650E
B6B7A45C 7E089FED 7FBA3442 82CAFBD6 F7E319F7 C0B0BD59 E2CA4BDB
556D61A5'],
            [SEC2CurveFactory::secp224r1(), '04 B70E0CBD 6BB4BF7F 321390B9 4A03C1D3 56C21122 343280D6
115C1D21 BD376388 B5F723FB 4C22DFE6 CD4375A0 5A074764 44D58199
85007E34'],
            [SEC2CurveFactory::secp256k1(), '04 79BE667E F9DCBBAC 55A06295 CE870B07 029BFCDB 2DCE28D9
59F2815B 16F81798 483ADA77 26A3C465 5DA4FBFC 0E1108A8 FD17B448
A6855419 9C47D08F FB10D4B8'],
            [SEC2CurveFactory::secp256r1(), '04 6B17D1F2 E12C4247 F8BCE6E5 63A440F2 77037D81 2DEB33A0
F4A13945 D898C296 4FE342E2 FE1A7F9B 8EE7EB4A 7C0F9E16 2BCE3357
6B315ECE CBB64068 37BF51F5'],
            [SEC2CurveFactory::secp384r1(), '04 AA87CA22 BE8B0537 8EB1C71E F320AD74 6E1D3B62 8BA79B98
59F741E0 82542A38 5502F25D BF55296C 3A545E38 72760AB7 3617DE4A
96262C6F 5D9E98BF 9292DC29 F8F41DBD 289A147C E9DA3113 B5F0B8C0
0A60B1CE 1D7E819D 7A431D7C 90EA0E5F'],
            [SEC2CurveFactory::secp521r1(), '04 00C6858E 06B70404 E9CD9E3E CB662395 B4429C64 8139053F
B521F828 AF606B4D 3DBAA14B 5E77EFE7 5928FE1D C127A2FF A8DE3348
B3C1856A 429BF97E 7E31C2E5 BD660118 39296A78 9A3BC004 5C8A5FB4
2C7D1BD9 98F54449 579B4468 17AFBD17 273E662C 97EE7299 5EF42640
C550B901 3FAD0761 353C7086 A272C240 88BE9476 9FD16650'],
        ];
    }

    /**
     * @dataProvider compressedGPoint
     */
    public function testGCorrectlyDecoded(Curve $curve, string $GHexWithSpaces): void
    {
        $point = $this->createPointFromSEC2Uncompressed($GHexWithSpaces);
        $this->assertObjectEquals($point, $curve->getG());
    }

    /**
     * Decompresses the point into its x and y coordinates.
     *
     * @param string $hexWithSpaces
     * @return Point
     */
    private function createPointFromSEC2Uncompressed(string $hexWithSpaces): Point
    {
        $hex = str_replace(" ", "", $hexWithSpaces);
        $points = substr($hex, 2);

        $coordinateLength = (int) (strlen($points) / 2);
        $x = gmp_init(substr($points, 0, $coordinateLength), 16);
        $y = gmp_init(substr($points, $coordinateLength), 16);

        return new Point($x, $y);
    }
}
