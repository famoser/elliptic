<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Random;

use Mdanter\Ecc\Legacy\Math\GmpMath;
use Mdanter\Ecc\Legacy\Primitives\CurveFp;
use Mdanter\Ecc\Legacy\Primitives\CurveParameters;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Mdanter\Ecc\Legacy\Random\DebugDecorator;
use Mdanter\Ecc\Legacy\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Tests\AbstractTestCase;

class RandomGeneratorFactoryTest extends AbstractTestCase
{
    public function testDebug()
    {
        $debugOn = true;

        $rng = RandomGeneratorFactory::getRandomGenerator($debugOn);
        $this->assertInstanceOf(DebugDecorator::class, $rng);
        $this->assertInstanceOf(\GMP::class, $rng->generate(gmp_init(111)));

        $adapter = new GmpMath();
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $adapter);
        $point = new GeneratorPoint($adapter, $curve, gmp_init(13, 10), gmp_init(7, 10), gmp_init(7, 10));

        $privateKey = $point->getPrivateKeyFrom(gmp_init(1));
        $rng = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, gmp_init(1), 'sha256', $debugOn);
        $this->assertInstanceOf(DebugDecorator::class, $rng);
        $this->assertInstanceOf(\GMP::class, $rng->generate(gmp_init(111)));

        ob_clean();
    }
}
