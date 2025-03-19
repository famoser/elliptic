<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Legacy\Exception;

class SquareRootException extends NumberTheoryException
{
    const CODE_GENERAL = 0;
    const CODE_JACOBI = 1;
}
