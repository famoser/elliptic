<?php

namespace Famoser\Elliptic\Curves;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * SEC2 curves from https://www.secg.org/sec2-v2.pdf (only the secp variants for now).
 *
 * For readability, copied verbatim.
 */
class SEC2CurveFactory
{
    /**
     * Decompresses the point into its x and y coordinates.
     *
     * @param string $hexWithSpaces
     * @return Point
     */
    private static function createPointFromSEC2Uncompressed(string $hexWithSpaces): Point
    {
        $hex = str_replace(" ", "", $hexWithSpaces);
        $points = substr($hex, 2);

        $coordinateLength = strlen($points) / 2;
        $x = gmp_init(substr($points, 0, $coordinateLength), 16);
        $y = gmp_init(substr($points, $coordinateLength), 16);

        return new Point($x, $y);
    }

    public static function secp192k1(): Curve
    {
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFEE37', 16);
        $a = gmp_init('00000000 00000000 00000000 00000000 00000000 00000000', 16);
        $b = gmp_init('00000000 00000000 00000000 00000000 00000000 00000003', 16);

        $G = self::createPointFromSEC2Uncompressed('04 DB4FF10E C057E9AE 26B07D02 80B7F434 1DA5D1B1 EAE06C7D
9B2F2F6D 9C5628A7 844163D0 15BE8634 4082AA88 D95E2F9D');

        $n = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFE 26F2FC17 0F69466A 74DEFD8D', 16);
        $h = gmp_init('01', 16);

        return new Curve(CurveType::ShortWeierstrass, $p, $a, $b, $G, $n, $h);
    }

    public static function secp192r1(): Curve
    {
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFFFFF FFFFFFFF', 16);
        $a = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFFFFF FFFFFFFC', 16);
        $b = gmp_init('64210519 E59C80E7 0FA7E9AB 72243049 FEB8DEEC C146B9B1', 16);

        $G = self::createPointFromSEC2Uncompressed('04 188DA80E B03090F6 7CBF20EB 43A18800 F4FF0AFD 82FF1012
07192B95 FFC8DA78 631011ED 6B24CDD5 73F977A1 1E794811');

        $n = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF 99DEF836 146BC9B1 B4D22831', 16);
        $h = gmp_init('01', 16);

        return new Curve(CurveType::ShortWeierstrass, $p, $a, $b, $G, $n, $h);
    }

    public static function secp224k1(): Curve
    {
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFE56D', 16);
        $a = gmp_init('00000000 00000000 00000000 00000000 00000000 00000000 00000000', 16);
        $b = gmp_init('00000000 00000000 00000000 00000000 00000000 00000000 00000005', 16);

        $G = self::createPointFromSEC2Uncompressed('04 A1455B33 4DF099DF 30FC28A1 69A467E9 E47075A9 0F7E650E
B6B7A45C 7E089FED 7FBA3442 82CAFBD6 F7E319F7 C0B0BD59 E2CA4BDB
556D61A5');

        $n = gmp_init('01 00000000 00000000 00000000 0001DCE8 D2EC6184 CAF0A971
769FB1F7', 16);
        $h = gmp_init('01', 16);

        return new Curve(CurveType::ShortWeierstrass, $p, $a, $b, $G, $n, $h);
    }

    public static function secp224r1(): Curve
    {
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF 00000000 00000000 00000001', 16);
        $a = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFFFFF FFFFFFFF FFFFFFFE', 16);
        $b = gmp_init('B4050A85 0C04B3AB F5413256 5044B0B7 D7BFD8BA 270B3943 2355FFB4', 16);

        $G = self::createPointFromSEC2Uncompressed('04 B70E0CBD 6BB4BF7F 321390B9 4A03C1D3 56C21122 343280D6
115C1D21 BD376388 B5F723FB 4C22DFE6 CD4375A0 5A074764 44D58199
85007E34');

        $n = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFF16A2 E0B8F03E 13DD2945 5C5C2A3D', 16);
        $h = gmp_init('01', 16);

        return new Curve(CurveType::ShortWeierstrass, $p, $a, $b, $G, $n, $h);
    }

    public static function secp256k1(): Curve
    {
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE
FFFFFC2F', 16);
        $a = gmp_init('00000000 00000000 00000000 00000000 00000000 00000000 00000000
00000000', 16);
        $b = gmp_init('00000000 00000000 00000000 00000000 00000000 00000000 00000000
00000007', 16);

        $G = self::createPointFromSEC2Uncompressed('04 79BE667E F9DCBBAC 55A06295 CE870B07 029BFCDB 2DCE28D9
59F2815B 16F81798 483ADA77 26A3C465 5DA4FBFC 0E1108A8 FD17B448
A6855419 9C47D08F FB10D4B8');

        $n = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE BAAEDCE6 AF48A03B BFD25E8C
D0364141', 16);
        $h = gmp_init('01', 16);

        return new Curve(CurveType::ShortWeierstrass, $p, $a, $b, $G, $n, $h);
    }

    public static function secp256r1(): Curve
    {
        $p = gmp_init('FFFFFFFF 00000001 00000000 00000000 00000000 FFFFFFFF FFFFFFFF
FFFFFFFF', 16);
        $a = gmp_init('FFFFFFFF 00000001 00000000 00000000 00000000 FFFFFFFF FFFFFFFF
FFFFFFFC', 16);
        $b = gmp_init('5AC635D8 AA3A93E7 B3EBBD55 769886BC 651D06B0 CC53B0F6 3BCE3C3E
27D2604B', 16);

        $G = self::createPointFromSEC2Uncompressed('04 6B17D1F2 E12C4247 F8BCE6E5 63A440F2 77037D81 2DEB33A0
F4A13945 D898C296 4FE342E2 FE1A7F9B 8EE7EB4A 7C0F9E16 2BCE3357
6B315ECE CBB64068 37BF51F5');

        $n = gmp_init('FFFFFFFF 00000000 FFFFFFFF FFFFFFFF BCE6FAAD A7179E84 F3B9CAC2
FC632551', 16);
        $h = gmp_init('01', 16);

        return new Curve(CurveType::ShortWeierstrass, $p, $a, $b, $G, $n, $h);
    }

    public static function secp384r1(): Curve
    {
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF
FFFFFFFE FFFFFFFF 00000000 00000000 FFFFFFFF', 16);
        $a = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF
FFFFFFFE FFFFFFFF 00000000 00000000 FFFFFFFC', 16);
        $b = gmp_init('B3312FA7 E23EE7E4 988E056B E3F82D19 181D9C6E FE814112 0314088F
5013875A C656398D 8A2ED19D 2A85C8ED D3EC2AEF', 16);

        $G = self::createPointFromSEC2Uncompressed('04 AA87CA22 BE8B0537 8EB1C71E F320AD74 6E1D3B62 8BA79B98
59F741E0 82542A38 5502F25D BF55296C 3A545E38 72760AB7 3617DE4A
96262C6F 5D9E98BF 9292DC29 F8F41DBD 289A147C E9DA3113 B5F0B8C0
0A60B1CE 1D7E819D 7A431D7C 90EA0E5F');

        $n = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF C7634D81
F4372DDF 581A0DB2 48B0A77A ECEC196A CCC52973', 16);
        $h = gmp_init('01', 16);

        return new Curve(CurveType::ShortWeierstrass, $p, $a, $b, $G, $n, $h);
    }

    public static function secp521r1(): Curve
    {
        $p = gmp_init('01FF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF
FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF
FFFFFFFF FFFFFFFF FFFFFFFF', 16);
        $a = gmp_init('01FF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF
FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF
FFFFFFFF FFFFFFFF FFFFFFFC', 16);
        $b = gmp_init('0051 953EB961 8E1C9A1F 929A21A0 B68540EE A2DA725B 99B315F3
B8B48991 8EF109E1 56193951 EC7E937B 1652C0BD 3BB1BF07 3573DF88
3D2C34F1 EF451FD4 6B503F00', 16);

        $G = self::createPointFromSEC2Uncompressed('04 00C6858E 06B70404 E9CD9E3E CB662395 B4429C64 8139053F
B521F828 AF606B4D 3DBAA14B 5E77EFE7 5928FE1D C127A2FF A8DE3348
B3C1856A 429BF97E 7E31C2E5 BD660118 39296A78 9A3BC004 5C8A5FB4
2C7D1BD9 98F54449 579B4468 17AFBD17 273E662C 97EE7299 5EF42640
C550B901 3FAD0761 353C7086 A272C240 88BE9476 9FD16650');

        $n = gmp_init('01FF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF
FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF
FFFFFFFF FFFFFFFF FFFFFFFF', 16);
        $h = gmp_init('01', 16);

        return new Curve(CurveType::ShortWeierstrass, $p, $a, $b, $G, $n, $h);
    }
}
