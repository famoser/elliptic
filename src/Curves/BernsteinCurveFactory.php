<?php

namespace Famoser\Elliptic\Curves;

use Famoser\Elliptic\Math\Primitives\PrimeField;
use Famoser\Elliptic\Primitives\BirationalMap;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * Montgomery curves from https://datatracker.ietf.org/doc/html/rfc7748.
 * Popularized for fast elliptic curve cryptography in https://cr.yp.to/ecdh/curve25519-20060209.pdf
 *
 * Defined using evaluated values for easy and fast consumption.
 * See unit tests to check that these variables indeed correspond to their definition (e.g., that p = 2^255 - 19).
 */
class BernsteinCurveFactory
{
    public static function curve25519(): Curve
    {
        // p = 2^255 - 19
        $p = gmp_init('7FFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFED', 16);
        $a = gmp_init(486662);
        $b = gmp_init(1);

        $u = gmp_init(9);
        $v = gmp_init('147816194475895447910205935684099868872646061346164752889648818
      37755586237401', 10);
        $P = new Point($u, $v);

        // order = 2^252 + 0x14def9dea2f79cd65812631a5cf5d3ed
        $order = gmp_init('10000000 00000000 00000000 00000000 14DEF9DE A2F79CD6 5812631A 5CF5D3ED', 16);
        $cofactor = gmp_init(8);

        return new Curve(CurveType::Montgomery, $p, $a, $b, $P, $order, $cofactor);
    }

    public static function curve25519ToEdwards25519(): BirationalMap
    {
        $p = gmp_init('7FFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFED', 16);
        $field = new PrimeField($p);

        // sqrt(-486664)
        // - calculated using sage: sqrt(GF(2^255-19)(-486664), all=True)
        // - then chosen the second value as it correctly converts the base points
        $squareRootOfMinus486664 = gmp_init('70D9120B 9F5FF944 2D84F723 FC03B081 3A5E2C2E B482E57D 3391FB55 00BA81E7', 16);

        // (x, y) = (sqrt(-486664)*u/v, (u-1)/(u+1))
        $map = static function (Point $point) use ($field, $squareRootOfMinus486664) {
            $x = $field->mul(
                $field->mul($squareRootOfMinus486664, $point->x),
                /** @phpstan-ignore-next-line */
                $field->invert($point->y)
            );
            $y = $field->mul(
                $field->sub($point->x, gmp_init(1)),
                /** @phpstan-ignore-next-line */
                $field->invert(
                    $field->add($point->x, gmp_init(1))
                )
            );

            return new Point($x, $y);
        };

        // (u, v) = ((1+y)/(1-y), sqrt(-486664)*u/x)
        $reverse = static function (Point $point) use ($field, $squareRootOfMinus486664) {
            $u = $field->mul(
                $field->add(gmp_init(1), $point->y),
                /** @phpstan-ignore-next-line */
                $field->invert(
                    $field->sub(gmp_init(1), $point->y)
                )
            );

            $v = $field->mul(
                $field->mul($squareRootOfMinus486664, $u),
                /** @phpstan-ignore-next-line */
                $field->invert($point->x)
            );

            return new Point($u, $v);
        };

        return new BirationalMap($map, $reverse);
    }

    public static function edwards25519(): Curve
    {
        // p = 2^255 - 19
        $p = gmp_init('7FFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFED', 16);
        // a = -1 mod p
        $a = gmp_init('7FFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFEC', 16);
        $d = gmp_init('370957059346694393431380835087545651895421138798432190163887855330
      85940283555', 10);

        $x = gmp_init('151122213495354007725011514095885315114540126930418572060461132
      83949847762202');
        $y = gmp_init('463168356949264781694283940034751631413079938662562256157830336
      03165251855960', 10);
        $P = new Point($x, $y);

        // order = 2^252 + 0x14def9dea2f79cd65812631a5cf5d3ed
        $order = gmp_init('10000000 00000000 00000000 00000000 14DEF9DE A2F79CD6 5812631A 5CF5D3ED', 16);
        $cofactor = gmp_init(8);

        return new Curve(CurveType::TwistedEdwards, $p, $a, $d, $P, $order, $cofactor);
    }

    public static function curve448(): Curve
    {
        // p = 2^448 - 2^224 - 1
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF', 16);
        $a = gmp_init(156326);
        $b = gmp_init(1);

        $u = gmp_init(5);
        $v = gmp_init('355293926785568175264127502063783334808976399387714271831880898
      435169088786967410002932673765864550910142774147268105838985595290
      606362', 10);
        $P = new Point($u, $v);

        // order = 2^446 - 0x8335dc163bb124b65129c96fde933d8d723a70aadc873d6d54a7bb0d
        $order = gmp_init('3FFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF 7CCA23E9 C44EDB49 AED63690 216CC272 8DC58F55 2378C292 AB5844F3', 16);
        $cofactor = gmp_init(8);

        return new Curve(CurveType::Montgomery, $p, $a, $b, $P, $order, $cofactor);
    }
}
