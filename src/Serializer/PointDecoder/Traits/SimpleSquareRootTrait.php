<?php

namespace Famoser\Elliptic\Serializer\PointDecoder\Traits;

use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;

trait SimpleSquareRootTrait
{
    /**
     * @throws PointDecoderException
     */
    public function simpleSquareRoot(\GMP $alpha, ?bool $isEven = null): \GMP
    {
        $p = $this->curve->getP();
        $pMod8 = gmp_mod($p, 8);
        if (gmp_cmp($pMod8, 5) === 0) {
            $beta = $this->squareRootForPMod85($alpha);
            return $this->isEvenCorrection($beta, $isEven);
        }

        $pMod4 = gmp_mod($pMod8, 4);
        if (gmp_cmp($pMod4, 3) === 0) {
            $beta = $this->squareRootForPMod43($alpha);
            return $this->isEvenCorrection($beta, $isEven);
        }

        throw new PointDecoderException('No general square root implemented (e.g. via Tonelli-Shanks), only for special cases p mod 8 = 5 and p mod 4 = 3.');
    }

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
    protected function squareRootForPMod43(\GMP $alpha): \GMP
    {
        $p = $this->curve->getP();

        $jacobiSymbol = gmp_jacobi($alpha, $p);
        if ($jacobiSymbol !== 1) {
            throw new PointDecoderException('No square root of alpha.');
        }

        $const = gmp_div(gmp_add($p, 1), 4);
        return gmp_powm($alpha, $const, $p);
    }

    /**
     * take the square root of alpha, while doing a (much cheaper) exponentiation
     *
     * observe that alpha^((p+3)/8) = y^((p+3)/4) = candidate
     * (p+3)/8 is an integer, as for our prime p it holds that p mod 8 = 5
     *
     * implements https://datatracker.ietf.org/doc/html/rfc8032#section-5.1.1
     */
    protected function squareRootForPMod85(\GMP $alpha): \GMP
    {
        $p = $this->curve->getP();

        $const = gmp_div(gmp_add($p, 3), 8);
        $candidate = gmp_powm($alpha, $const, $p);

        $candidateSquare = gmp_powm($candidate, 2, $p);
        if (gmp_cmp($candidateSquare, $alpha) === 0) {
            return $candidate;
        } else {
            $check = gmp_mod(gmp_add($candidateSquare, $alpha), $p);
            if (gmp_cmp($check, 0) === 0) {
                $const = gmp_div(gmp_sub($p, 1), 4);
                $correctionFactor = gmp_powm(2, $const, $p);
                $correctedCandidate = gmp_mod(gmp_mul($candidate, $correctionFactor), $p);

                return $correctedCandidate;
            }

            throw new PointDecoderException('No square root of alpha.');
        }
    }

    /**
     * corrects the sign of the element, if specified
     *
     * used e.g. for https://datatracker.ietf.org/doc/html/rfc8032#section-5.2.3 point 4
     */
    protected function isEvenCorrection(\GMP $beta, ?bool $isEven = null): \GMP
    {
        if ($isEven === null) {
            return $beta;
        }

        $yp = $isEven ? gmp_init(0) : gmp_init(1);
        if (gmp_cmp(gmp_mod($beta, 2), $yp) === 0) {
            return $beta;
        } else {
            return gmp_sub($this->curve->getP(), $beta);
        }
    }
}
