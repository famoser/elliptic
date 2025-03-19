<?php

namespace Mdanter\Ecc\Tests\Serializer\Point;

use Mdanter\Ecc\Legacy\Curves\NamedCurveFp;
use Mdanter\Ecc\Legacy\EccFactory;
use Mdanter\Ecc\Legacy\Exception\UnsupportedCurveException;
use Mdanter\Ecc\Legacy\Primitives\CurveParameters;
use Mdanter\Ecc\Legacy\Serializer\Point\PointSize;
use PHPUnit\Framework\TestCase;

class PointSizeTest extends TestCase
{
    public function testValidCurve()
    {
        $nistCurve = EccFactory::getNistCurves()->curve521();
        $this->assertEquals(66, PointSize::getByteSize($nistCurve));
    }

    public function testInvalidCurve()
    {
        $this->expectException(UnsupportedCurveException::class);
        $adapter = EccFactory::getAdapter();
        $curve = new NamedCurveFp('badcurve', new CurveParameters(10, gmp_init(1), gmp_init(1), gmp_init(1)), $adapter);
        PointSize::getByteSize($curve);
    }
}
