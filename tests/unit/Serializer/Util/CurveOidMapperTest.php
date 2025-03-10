<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\Util;

use Sop\ASN1\Type\Primitive\ObjectIdentifier;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Tests\AbstractTestCase;

class CurveOidMapperTest extends AbstractTestCase
{
    public function testGetNames()
    {
        if (version_compare(\PHPUnit\Runner\Version::id(), '7.0.0') >= 0) {
            $this->assertIsArray(CurveOidMapper::getNames());
        } else {
            $this->assertInternalType('array', CurveOidMapper::getNames());
        }
    }

    public function testValidValues()
    {
        $nistCurve = EccFactory::getNistCurves()->curve521();
        $G = EccFactory::getNistCurves()->generator521();
        $nistp521oid = CurveOidMapper::getCurveOid($nistCurve);
        $this->assertEquals(66, CurveOidMapper::getByteSize($nistCurve));
        $this->assertInstanceOf(ObjectIdentifier::class, $nistp521oid);

        $curve = CurveOidMapper::getCurveFromOid($nistp521oid);
        $this->assertTrue($curve->equals($nistCurve));

        $gen = CurveOidMapper::getGeneratorFromOid($nistp521oid);
        $this->assertTrue($G->equals($gen));
    }

    public function testGetBytesUnknownCurve()
    {
        $this->expectException(\Mdanter\Ecc\Exception\UnsupportedCurveException::class);
        $adapter = EccFactory::getAdapter();
        $curve = new NamedCurveFp('badcurve', new CurveParameters(10, gmp_init(1), gmp_init(1), gmp_init(1)), $adapter);
        CurveOidMapper::getByteSize($curve);
    }

    public function testGetCurveOid()
    {
        $this->expectException(\Mdanter\Ecc\Exception\UnsupportedCurveException::class);
        $adapter = EccFactory::getAdapter();
        $curve = new NamedCurveFp('badcurve', new CurveParameters(10, gmp_init(1), gmp_init(1), gmp_init(1)), $adapter);
        CurveOidMapper::getCurveOid($curve);
    }

    public function testCurveUnknownOid()
    {
        $this->expectException(\Mdanter\Ecc\Exception\UnsupportedCurveException::class);
        $oid = new ObjectIdentifier('1.3');
        CurveOidMapper::getCurveFromOid($oid);
    }

    public function testGeneratorUnknownOid()
    {
        $this->expectException(\Mdanter\Ecc\Exception\UnsupportedCurveException::class);
        $oid = new ObjectIdentifier('1.3');
        CurveOidMapper::getGeneratorFromOid($oid);
    }
}
