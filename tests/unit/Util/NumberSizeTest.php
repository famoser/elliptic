<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Util;

use Mdanter\Ecc\Legacy\Math\GmpMathInterface;
use Mdanter\Ecc\Legacy\Util\NumberSize;
use Mdanter\Ecc\Tests\AbstractTestCase;

class NumberSizeTest extends AbstractTestCase
{

    public function getBnNumBitsNumbers()
    {
        return $this->_getAdapters(array(
            array('0', 0),
            array('0x100', 9),
            array('0x00000432', 11),
        ));
    }

    /**
     * @dataProvider getBnNumBitsNumbers
     * @param GmpMathInterface $adapter
     * @param string $number hex number
     * @param int $expected
     */
    public function testNumBits(GmpMathInterface $adapter, $number, $expected)
    {
        $size = NumberSize::bnNumBits($adapter, gmp_init($number, 16));

        $this->assertEquals($expected, $size);
    }

    public function getBnNumBytesNumbers()
    {
        return $this->_getAdapters(array(
            array('0', 0),
            array('0x00000432', 2),
            array('0x2e224bd065fead1218f3608d4e74837b6096d11c4fff4139cd41d9df03cfcb270df7a9ae6f628819c3ae744db4189b1330cb2ee4eea7d5515b282dee59e21dcf1e', 65),
        ));
    }

    /**
     * @dataProvider getBnNumBytesNumbers
     * @param GmpMathInterface $adapter
     * @param string $number hex number
     * @param int $expected
     */
    public function testNumBytes(GmpMathInterface $adapter, $number, $expected)
    {
        $size = NumberSize::bnNumBytes($adapter, gmp_init($number, 16));

        $this->assertEquals($expected, $size);
    }
}
