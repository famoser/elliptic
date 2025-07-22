<?php

namespace Famoser\Elliptic\Serializer\PointDecoder\Traits;

use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;

trait FromXCoordinateTrait
{
    use PMod43RecoveryTrait;
    use PMod85RecoveryTrait;

    /**
     * @throws PointDecoderException
     */
    public function fromXCoordinate(\GMP $x, ?bool $isEvenY = null): Point
    {
        $p = $this->curve->getP();
        $pMod8 = gmp_mod($p, 8);
        if (gmp_cmp($pMod8, 5) === 0) {
            return $this->recoverXForPMod85($x);
        }

        $pMod4 = gmp_mod($pMod8, 4);
        if (gmp_cmp($pMod4, 3) === 0) {
            if ($isEvenY === null) {
                throw new PointDecoderException('Point decoding for p mod 4 = 3 needs isEvenY to be defined.');
            }

            $beta = $this->recoverXForPMod43($x);

            $yp = $isEvenY ? gmp_init(0) : gmp_init(1);
            if (gmp_cmp(gmp_mod($beta, 2), $yp) === 0) {
                return new Point($x, $beta);
            } else {
                return new Point($x, gmp_sub($p, $beta));
            }
        }

        throw new PointDecoderException('No general point decoding implemented (e.g. via Tonelli-Shanks), only for special cases p mod 8 = 5 and p mod 4 = 3.');
    }
}
