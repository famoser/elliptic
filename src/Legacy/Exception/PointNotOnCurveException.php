<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Legacy\Exception;

use Mdanter\Ecc\Legacy\Primitives\CurveFpInterface;

class PointNotOnCurveException extends PointException
{
    public function __construct(\GMP $x, \GMP $y, CurveFpInterface $curve)
    {
        parent::__construct("Curve " . $curve . " does not contain point (" . gmp_strval($x, 10) . ", " . gmp_strval($y, 10) . ")");
    }
}
