<?php

namespace Famoser\Elliptic\Curves;

use Famoser\Elliptic\Math\MathInterface;
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
        /**
         * sage:
         * F = GF(2^255 - 19)
         * e = EllipticCurve(F, [0, 486662, 0, 1, 0])
         * u = e(9, 43114425171068552920764898935933967039370386198203806730763910166200978582548)
         */

        // p = 2^255 - 19
        $p = gmp_init('7FFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFED', 16);
        $a = gmp_init(486662);
        $b = gmp_init(1);

        $u = gmp_init(9);
        // see https://www.rfc-editor.org/errata/eid4730; the v in the RFC is incorrect
        $v = gmp_init('431144251710685529207648989359339670393703861982038067307639101
66200978582548', 10);
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
        $squareRootOfMinus486664 = gmp_init('F26EDF46 0A006BBD 27B08DC0 3FC4F7EC 5A1D3D14 B7D1A82C C6E04AAF F457E06', 16);

        // (x, y) = (sqrt(-486664)*u/v, (u-1)/(u+1))
        $map = static function (MathInterface $math, Point $point) use ($field, $squareRootOfMinus486664) {
            $x = $field->mul(
                $field->mul($squareRootOfMinus486664, $point->x),
                /** @phpstan-ignore-next-line */
                $field->invert($point->y)
            );

            // if x is 0, short-cut to resolve y = 1 (following the normal flow would choose the other y^2 root)
            if (gmp_cmp($x, 0) === 0) {
                return $math->getInfinity();
            }

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
        $reverse = static function (MathInterface $math, Point $point) use ($field, $squareRootOfMinus486664) {
            $divisor = $field->sub(gmp_init(1), $point->y);
            if (gmp_cmp($divisor, 0) === 0) {
                return $math->getInfinity();
            }
            if (gmp_cmp($point->x, 0) === 0) {
                return $math->getInfinity();
            }

            $u = $field->mul(
                $field->add(gmp_init(1), $point->y),
                /** @phpstan-ignore-next-line */
                $field->invert($divisor)
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
        /**
         * sage:
         * F = GF(2^448 - 2^224 - 1)
         * e = EllipticCurve(F, [0, 156326, 0, 1, 0])
         * u = e(5, 355293926785568175264127502063783334808976399387714271831880898435169088786967410002932673765864550910142774147268105838985595290606362)
         */

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
        $cofactor = gmp_init(4);

        return new Curve(CurveType::Montgomery, $p, $a, $b, $P, $order, $cofactor);
    }

    public static function curve448ToEdwards(): BirationalMap
    {
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF', 16);
        $field = new PrimeField($p);

        // sqrt(156324)
        // - calculated using sage: sqrt(GF(2^448-2^224-1)(156324), all=True)
        // - then chosen the first value as it correctly converts the base points
        $squareRootOf15634 = gmp_init('45B2C5F7 D649EED0 77ED1AE4 5F44D541 43E34F71 4B71AA96 C945AF01 2D182975 0734CDE9 FADDBDA4 C066F7ED 54419CA5 2C85DE1E 8AAE4E6C', 16);

        // (x, y) = (sqrt(156324)*u/v, (1+u)/(1-u))
        $map = static function (MathInterface $math, Point $point) use ($field, $squareRootOf15634) {
            $x = $field->mul(
                $field->mul($squareRootOf15634, $point->x),
                /** @phpstan-ignore-next-line */
                $field->invert($point->y)
            );
            // note: if x === 0, then y === 1 (as opposed to the other map from curve25519 to edwards25519
            $y = $field->mul(
                $field->add(gmp_init(1), $point->x),
                /** @phpstan-ignore-next-line */
                $field->invert(
                    $field->sub(gmp_init(1), $point->x)
                )
            );

            return new Point($x, $y);
        };

        // (u, v) = ((y-1)/(y+1), sqrt(156324)*u/x)
        $reverse = static function (MathInterface $math, Point $point) use ($field, $squareRootOf15634) {
            $divisor = $field->add($point->y, gmp_init(1));
            if (gmp_cmp($divisor, 0) === 0) {
                return $math->getInfinity();
            }
            if (gmp_cmp($point->x, 0) === 0) {
                return $math->getInfinity();
            }

            $u = $field->mul(
                $field->sub($point->y, gmp_init(1)),
                /** @phpstan-ignore-next-line */
                $field->invert($divisor)
            );

            $v = $field->mul(
                $field->mul($squareRootOf15634, $u),
                /** @phpstan-ignore-next-line */
                $field->invert($point->x)
            );

            return new Point($u, $v);
        };

        return new BirationalMap($map, $reverse);
    }

    public static function curve448Edwards(): Curve
    {
        // p = 2^448 - 2^224 - 1
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF', 16);
        // a = 1 mod p
        $a = gmp_init('1', 10);
        $d = gmp_init('611975850744529176160423220965553317543219696871016626328968936415
      087860042636474891785599283666020414768678979989378147065462815545
      017', 10);

        $x = gmp_init('345397493039729516374008604150537410266655260075183290216406970
      281645695073672344430481787759340633221708391583424041788924124567
      700732', 10);
        $y = gmp_init('363419362147803445274661903944002267176820680343659030140745099
      590306164083365386343198191849338272965044442230921818680526749009
      182718', 10);
        $P = new Point($x, $y);

        // order = 2^446 - 0x8335dc163bb124b65129c96fde933d8d723a70aadc873d6d54a7bb0d
        $order = gmp_init('3FFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF 7CCA23E9 C44EDB49 AED63690 216CC272 8DC58F55 2378C292 AB5844F3', 16);
        $cofactor = gmp_init(4);

        return new Curve(CurveType::Edwards, $p, $a, $d, $P, $order, $cofactor);
    }

    public static function edwards448(): Curve
    {
        // p = 2^448 - 2^224 - 1
        $p = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF', 16);
        // a = 1 mod p
        $a = gmp_init('1', 10);
        $d = gmp_init('FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFE FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFF6756 ', 16);

        $x = gmp_init('224580040295924300187604334099896036246789641632564134246125461
      686950415467406032909029192869357953282578032075146446173674602635
      247710', 10);
        $y = gmp_init('298819210078481492676017930443930673437544040154080242095928241
      372331506189835876003536878655418784733982303233503462500531545062
      832660', 10);
        $P = new Point($x, $y);

        // order = 2^446 - 0x8335dc163bb124b65129c96fde933d8d723a70aadc873d6d54a7bb0d
        $order = gmp_init('3FFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF FFFFFFFF 7CCA23E9 C44EDB49 AED63690 216CC272 8DC58F55 2378C292 AB5844F3', 16);
        $cofactor = gmp_init(4);

        return new Curve(CurveType::Edwards, $p, $a, $d, $P, $order, $cofactor);
    }
}
