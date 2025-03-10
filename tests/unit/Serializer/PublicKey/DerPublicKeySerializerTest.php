<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\PublicKey;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Sop\ASN1\Type\Primitive\BitString;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Primitive\ObjectIdentifier;
use Sop\ASN1\Type\Constructed\Sequence;

class DerPublicKeySerializerTest extends AbstractTestCase
{
    public function testFirstFailure()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('SEQUENCE expected, got primitive INTEGER.');

        $asn = new Integer(1);
        $binary = $asn->toDER();

        $serializer = DerPublicKeySerializer::create();
        $serializer->parse($binary);
    }

    public function testInvalidEcdsaOid()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid data: non X509 data.');

        $sequence = new Sequence(
            new Sequence(
                new ObjectIdentifier('1.1.1.1.1'),
                CurveOidMapper::getCurveOid(CurveFactory::getCurveByName('nistp192'))
            ),
            new BitString('04188DA80EB03090F67CBF20EB43A18800F4FF0AFD82FF101207192B95FFC8DA78631011ED6B24CDD573F977A11E794811')
        );
        $binary = $sequence->toDER();

        $serializer = DerPublicKeySerializer::create();
        $serializer->parse($binary);
    }

    public function testInvalidCurve()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not implemented for unnamed curves');

        $adapter = EccFactory::getAdapter();
        $p = gmp_init('6277101735386680763835789423207666416083908700390324961279', 10);
        $b = gmp_init('64210519e59c80e70fa7e9ab72243049feb8deecc146b9b1', 16);

        $parameters = new CurveParameters(192, $p, gmp_init('-3', 10), $b);
        $curve = new CurveFp($parameters, $adapter);

        $order = gmp_init('6277101735386680763835789423176059013767194773182842284081', 10);

        $x = gmp_init('188da80eb03090f67cbf20eb43a18800f4ff0afd82ff1012', 16);
        $y = gmp_init('07192b95ffc8da78631011ed6b24cdd573f977a11e794811', 16);

        $generator = $curve->getGenerator($x, $y, $order);
        $private = $generator->getPrivateKeyFrom(gmp_init(12));
        $public = $private->getPublicKey();

        $serializer = DerPublicKeySerializer::create();
        $serializer->serialize($public);
    }
}
