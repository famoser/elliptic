<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\Point;

use Mdanter\Ecc\Legacy\EccFactory;
use Mdanter\Ecc\Legacy\Serializer\Point\Format\CompressedPointSerializer;
use Mdanter\Ecc\Legacy\Serializer\Point\PointDecodingException;
use Mdanter\Ecc\Tests\AbstractTestCase;

class CompressedPointSerializerTest extends AbstractTestCase
{
    public function testChecksPrefix()
    {
        $this->expectException(PointDecodingException::class);
        $this->expectExceptionMessage('Invalid data: only compressed keys are supported.');
        $data = '01aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $serializer = new CompressedPointSerializer(EccFactory::getAdapter());
        $serializer->deserialize(EccFactory::getNistCurves()->curve192(), $data);
    }
}
