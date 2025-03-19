<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\Point;

use Mdanter\Ecc\Legacy\EccFactory;
use Mdanter\Ecc\Legacy\Serializer\Point\Format\UncompressedPointSerializer;
use Mdanter\Ecc\Serializer\PointDecoderException;
use Mdanter\Ecc\Tests\AbstractTestCase;

class UncompressedPointSerializerTest extends AbstractTestCase
{
    public function testChecksPrefix()
    {
        $this->expectException(PointDecoderException::class);
        $this->expectExceptionMessage('Invalid data: only uncompressed keys are supported.');
        $data = '01aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $serializer = new UncompressedPointSerializer();
        $serializer->deserialize(EccFactory::getNistCurves()->curve192(), $data);
    }
}
