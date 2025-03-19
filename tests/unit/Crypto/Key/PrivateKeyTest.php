<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Crypto\Key;

use Mdanter\Ecc\Integration\Utils\EcDH\EcDH;
use Mdanter\Ecc\Integration\Utils\Key\PublicKey;
use Mdanter\Ecc\Legacy\EccFactory;
use Mdanter\Ecc\Legacy\Primitives\CurveFp;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PrivateKeyTest extends AbstractTestCase
{
    public function testInstance()
    {
        $nist = EccFactory::getNistCurves();

        $generator = $nist->generator521();
        $curve = $generator->getCurve();

        $key = $generator->createPrivateKey();
        $this->assertInstanceOf(PublicKey::class, $key->getPublicKey());
        $this->assertInstanceOf(GeneratorPoint::class, $key->getPoint());
        $this->assertSame($generator, $key->getPoint());
        $this->assertInstanceOf(CurveFp::class, $key->getCurve());
        $this->assertSame($curve, $key->getCurve());
        $this->assertInstanceOf(\GMP::class, $key->getSecret());
        $this->assertInstanceOf(EcDH::class, $key->createExchange());
    }
}
