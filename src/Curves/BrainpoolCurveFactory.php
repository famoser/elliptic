<?php

namespace Famoser\Elliptic\Curves;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * Brainpoll curves from https://datatracker.ietf.org/doc/html/rfc5639#section-3.1.
 *
 * For readability, copied verbatim.
 */
class BrainpoolCurveFactory
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

        $coordinateLength = (int) (strlen($points) / 2);
        $x = gmp_init(substr($points, 0, $coordinateLength), 16);
        $y = gmp_init(substr($points, $coordinateLength), 16);

        return new Point($x, $y);
    }

    public static function P256r1(): Curve
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
}
