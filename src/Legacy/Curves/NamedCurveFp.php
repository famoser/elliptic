<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Legacy\Curves;

use Mdanter\Ecc\Legacy\Math\GmpMathInterface;
use Mdanter\Ecc\Legacy\Primitives\CurveFp;
use Mdanter\Ecc\Legacy\Primitives\CurveParameters;

class NamedCurveFp extends CurveFp
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string           $name
     * @param CurveParameters  $parameters
     * @param GmpMathInterface $adapter
     */
    public function __construct(string $name, CurveParameters $parameters, GmpMathInterface $adapter)
    {
        $this->name = $name;

        parent::__construct($parameters, $adapter);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
