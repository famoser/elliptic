<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Serializer\PublicKey;

use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class PemPublicKeySerializerTest extends AbstractTestCase
{
    public function testReadsPem()
    {
        $der = file_get_contents(__DIR__ . "/../../../data/openssl-secp256r1.1.pub.pem");
        $adapter = EccFactory::getAdapter();
        $derSerializer = DerPublicKeySerializer::create();
        $pemSerializer = new PemPublicKeySerializer($derSerializer);
        $key = $pemSerializer->parse($der);
        $this->assertInstanceOf(PublicKey::class, $key);
    }

    public function testConsistent()
    {
        $adapter = EccFactory::getAdapter();
        $G = EccFactory::getNistCurves($adapter)->generator192();
        $pubkey = $G->createPrivateKey()->getPublicKey();

        $serializer = new PemPublicKeySerializer(DerPublicKeySerializer::create());
        $serialized = $serializer->serialize($pubkey);
        $parsed = $serializer->parse($serialized);
        $this->assertTrue($pubkey->getPoint()->equals($parsed->getPoint()));
        $this->assertEquals($pubkey->getCurve(), $parsed->getCurve());
        $this->assertEquals($pubkey->getGenerator(), $parsed->getGenerator());
    }
}
