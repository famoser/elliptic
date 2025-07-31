<?php

namespace Famoser\Elliptic\Serializer\PointDecoder\Traits;

use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;

trait PMod85RecoveryTrait
{
    /**
     * take the square root of alpha, while doing a (much cheaper) exponentiation
     *
     * observe that alpha^((p+3)/8) = y^((p+3)/4) = candidate
     * (p+3)/8 is an integer, as for our prime p it holds that p mod 8 = 5
     *
     * implements https://datatracker.ietf.org/doc/html/rfc8032#section-5.1.1
     */
    protected function recoverXForPMod85(\GMP $x): Point
    {
        $p = $this->curve->getP();
        $alpha = gmp_mod($this->calculateAlpha($x), $p);

        $const = gmp_div(gmp_add($p, 3), 8);
        $candidate = gmp_powm($alpha, $const, $p);

        $candidateSquare = gmp_powm($candidate, 2, $p);
        if (gmp_cmp($candidateSquare, $alpha) === 0) {
            return new Point($x, $candidate);
        } else {
            $check = gmp_mod(gmp_add($candidateSquare, $alpha), $p);
            if (gmp_cmp($check, 0) === 0) {
                $const = gmp_div(gmp_sub($p, 1), 4);
                $correctionFactor = gmp_powm(2, $const, $p);
                $correctedCandidate = gmp_mod(gmp_mul($candidate, $correctionFactor), $p);

                return new Point($x, $correctedCandidate);
            }

            throw new PointDecoderException('No square root of alpha.');
        }
    }
}
