<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\Legacy\EccFactory;
use Mdanter\Ecc\Legacy\Math\GmpMathInterface;
use Mdanter\Ecc\Legacy\Primitives\CurveFpInterface;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Mdanter\Ecc\Legacy\Primitives\PointInterface;
use Mdanter\Ecc\Tests\AbstractTestCase;

class SecCurveTest extends AbstractTestCase
{

    public function getCurveParams()
    {
        return $this->_getAdapters([
            [ 'curve192k1', '0', '3', '6277101735386680763835789423207666416102355444459739541047'],
            [ 'curve256k1', '0', '7', '115792089237316195423570985008687907853269984665640564039457584007908834671663' ],
            [ 'curve256r1', '115792089210356248762697446949407573530086143415290314195533631308867097853948', '41058363725152142129326129780047268409114441015993725554835256314039467401291', '115792089210356248762697446949407573530086143415290314195533631308867097853951' ],
            [ 'curve384r1', '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112316', '27580193559959705877849011840389048093056905856361568521428707301988689241309860865136260764883745107765439761230575', '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319' ],
        ]);
    }

    /**
     * @param GmpMathInterface $math
     * @param string $function
     * @param string $a
     * @param string $b
     * @param string $prime
     * @dataProvider getCurveParams
     */
    public function testCurveGeneration(GmpMathInterface $math, string $function, string $a, string $b, string $prime)
    {
        $factory = EccFactory::getSecgCurves($math);
        /** @var CurveFpInterface $curve */
        $curve = $factory->{$function}();

        $this->assertInstanceOf(CurveFpInterface::class, $curve);
        $this->assertEquals($a, $math->toString($curve->getA()));
        $this->assertEquals($b, $math->toString($curve->getB()));
        $this->assertEquals($prime, $math->toString($curve->getPrime()));
    }

    public function getGeneratorParams()
    {
        return $this->_getAdapters([
            [ 'generator192k1', '6277101735386680763835789423061264271957123915200845512077', '6277101735386680763835789423207666416102355444459739541047' ],
            [ 'generator256k1', '115792089237316195423570985008687907852837564279074904382605163141518161494337', '115792089237316195423570985008687907853269984665640564039457584007908834671663' ],
            [ 'generator256r1', '115792089210356248762697446949407573529996955224135760342422259061068512044369', '115792089210356248762697446949407573530086143415290314195533631308867097853951' ],
            [ 'generator384r1', '39402006196394479212279040100143613805079739270465446667946905279627659399113263569398956308152294913554433653942643', '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319' ],
        ]);
    }

    /**
     * @param GmpMathInterface $math
     * @param string $function
     * @param string $order
     * @param string $prime
     * @dataProvider getGeneratorParams
     */
    public function testGeneratorGeneration(GmpMathInterface $math, string $function, string $order, string $prime)
    {
        $factory = EccFactory::getSecgCurves($math);
        /** @var GeneratorPoint $generator */
        $generator = $factory->{$function}();

        $this->assertInstanceOf(PointInterface::class, $generator);
        $this->assertEquals($order, $math->toString($generator->getOrder()));
        $this->assertEquals($prime, $math->toString($generator->getCurve()->getPrime()));
    }
}
