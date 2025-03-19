<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Crypto\Key;

use Mdanter\Ecc\Integration\Utils\Key\PublicKey;
use Mdanter\Ecc\Legacy\EccFactory;
use Mdanter\Ecc\Legacy\Exception\PublicKeyException;
use Mdanter\Ecc\Legacy\Primitives\CurveFp;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Mdanter\Ecc\Legacy\Primitives\Point;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PublicKeyTest extends AbstractTestCase
{
    public function testBadPointForGenerator()
    {
        $this->expectException(\Mdanter\Ecc\Legacy\Exception\PublicKeyException::class);
        $this->expectExceptionMessage('Point has x and y out of range');

        $adapter = EccFactory::getAdapter();
        $generator192 = EccFactory::getNistCurves($adapter)->generator192();
        $generator384 = EccFactory::getNistCurves($adapter)->generator384();

        $tooLarge = $generator384->createPrivateKey()->getPublicKey()->getPoint();
        try {
            new PublicKey($adapter, $generator192, $tooLarge);
        } catch (PublicKeyException $e) {
            $this->assertEquals($e->getGenerator(), $generator192);
            $this->assertEquals($e->getPoint(), $tooLarge);
            throw $e;
        }
    }

    public function testPointGeneratorMismatch()
    {
        $this->expectException(\Mdanter\Ecc\Legacy\Exception\PublicKeyException::class);
        $this->expectExceptionMessage('Curve for given point not in common with GeneratorPoint');

        $adapter = EccFactory::getAdapter();
        $generator384 = EccFactory::getNistCurves($adapter)->generator384();

        $generator192 = EccFactory::getNistCurves($adapter)->generator192();
        $mismatchPoint = $generator192->createPrivateKey()->getPublicKey()->getPoint();

        try {
            new PublicKey($adapter, $generator384, $mismatchPoint);
        } catch (PublicKeyException $e) {
            $this->assertEquals($e->getGenerator(), $generator384);
            $this->assertEquals($e->getPoint(), $mismatchPoint);
            throw $e;
        }
    }

    public function testInstance()
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves($adapter)->generator192();
        $curve = $generator->getCurve();
        $point = $generator->createPrivateKey()->getPublicKey()->getPoint();
        $key = new PublicKey($adapter, $generator, $point);

        $this->assertInstanceOf(CurveFp::class, $key->getCurve());
        $this->assertSame($curve, $key->getCurve());
        $this->assertInstanceOf(GeneratorPoint::class, $key->getGenerator());
        $this->assertSame($generator, $key->getGenerator());
        $this->assertInstanceOf(Point::class, $key->getPoint());
        $this->assertSame($point, $key->getPoint());
    }
}
