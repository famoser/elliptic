<?php

namespace Famoser\Elliptic\Curves;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Primitives\QuadraticTwist;

/**
 * Brainpool curves from https://datatracker.ietf.org/doc/html/rfc5639#section-3.1.
 *
 * For readability, copied verbatim.
 */
class BrainpoolCurveFactory
{
    public static function p160r1(): Curve
    {
        $p = gmp_init('E95E4A5F737059DC60DFC7AD95B3D8139515620F', 16);
        $A = gmp_init('340E7BE2A280EB74E2BE61BADA745D97E8F7C300', 16);
        $B = gmp_init('1E589A8595423412134FAA2DBDEC95C8D8675E58', 16);
        $x = gmp_init('BED5AF16EA3F6A4F62938C4631EB5AF7BDBCDBC3', 16);
        $y = gmp_init('1667CB477A1A8EC338F94741669C976316DA6321', 16);
        $q = gmp_init('E95E4A5F737059DC60DF5991D45029409E60FC09', 16);
        $h = gmp_init('1', 16);

        $G = new Point($x, $y);
        return new Curve(CurveType::ShortWeierstrass, $p, $A, $B, $G, $q, $h);
    }

    public static function p256r1(): Curve
    {
        $p = gmp_init('A9FB57DBA1EEA9BC3E660A909D838D726E3BF623D52620282013481D1F6E5377', 16);
        $A = gmp_init('7D5A0975FC2C3057EEF67530417AFFE7FB8055C126DC5C6CE94A4B44F330B5D9', 16);
        $B = gmp_init('26DC5C6CE94A4B44F330B5D9BBD77CBF958416295CF7E1CE6BCCDC18FF8C07B6', 16);
        $x = gmp_init('8BD2AEB9CB7E57CB2C4B482FFC81B7AFB9DE27E1E3BD23C23A4453BD9ACE3262', 16);
        $y = gmp_init('547EF835C3DAC4FD97F8461A14611DC9C27745132DED8E545C1D54C72F046997', 16);
        $q = gmp_init('A9FB57DBA1EEA9BC3E660A909D838D718C397AA3B561A6F7901E0E82974856A7', 16);
        $h = gmp_init('1', 16);

        $G = new Point($x, $y);
        return new Curve(CurveType::ShortWeierstrass, $p, $A, $B, $G, $q, $h);
    }

    public static function p256r1TwistToP256t1(): QuadraticTwist
    {
        $Z = gmp_init('3E2D4BD9597B58639AE7AA669CAB9837CF5CF20A2C852D10F655668DFC150EF0', 16);

        return new QuadraticTwist($Z);
    }

    public static function p256t1(): Curve
    {
        $p = gmp_init('A9FB57DBA1EEA9BC3E660A909D838D726E3BF623D52620282013481D1F6E5377', 16);
        $A = gmp_init('A9FB57DBA1EEA9BC3E660A909D838D726E3BF623D52620282013481D1F6E5374', 16);
        $B = gmp_init('662C61C430D84EA4FE66A7733D0B76B7BF93EBC4AF2F49256AE58101FEE92B04', 16);
        $x = gmp_init('A3E8EB3CC1CFE7B7732213B23A656149AFA142C47AAFBC2B79A191562E1305F4', 16);
        $y = gmp_init('2D996C823439C56D7F7B22E14644417E69BCB6DE39D027001DABE8F35B25C9BE', 16);
        $q = gmp_init('A9FB57DBA1EEA9BC3E660A909D838D718C397AA3B561A6F7901E0E82974856A7', 16);
        $h = gmp_init('1', 16);

        $G = new Point($x, $y);
        return new Curve(CurveType::ShortWeierstrass, $p, $A, $B, $G, $q, $h);
    }
}
