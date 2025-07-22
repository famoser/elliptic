<?php

namespace Famoser\Elliptic\Serializer\PointDecoder\Traits;

use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;

trait PMod43RecoveryTrait
{
    /**
     * take the square root of alpha, while doing a (much cheaper) exponentiation
     *
     *  observe that alpha^((p+1)/4) = y^((p+1)/2) = y^((p-1)/2) * y = y
     *  (p+1)/4 is an integer, as for our prime p it holds that p mod 4 = 3
     *  alpha = y^2 by the jacobi symbol check above that asserts y is a quadratic residue
     *  y^((p-1)/2) = 1 by Euler's Criterion applies to the quadratic residue y
     *
     * implements https://www.secg.org/sec1-v2.pdf 2.3.4
     *
     * same trick as described in https://datatracker.ietf.org/doc/html/rfc8032#section-5.2.1
     * but with a jacobi symbol to check for square root beforehand, instead of calculating x^2 at the end
     */
    protected function recoverXForPMod43(\GMP $x): \GMP
    {
        $p = $this->curve->getP();
        $alpha = $this->calculateAlpha($x);

        $jacobiSymbol = gmp_jacobi($alpha, $p);
        if ($jacobiSymbol !== 1) {
            throw new PointDecoderException('No square root of alpha.');
        }

        $const = gmp_div(gmp_add($p, 1), 4);
        return gmp_powm($alpha, $const, $p);
    }
}
