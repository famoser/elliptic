<?php

namespace Mdanter\Ecc\Integration\Utils;

use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Integration\Utils\DER\DerPublicKeySerializer;
use Mdanter\Ecc\Integration\Utils\DER\DerSignatureSerializer;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testDERPublicKeySerializerReads()
    {
        $pem = file_get_contents(__DIR__ . "/fixtures/openssl-secp256r1.1.pub.pem");
        $pemLines = explode("\n", $pem);
        array_shift($pemLines); // remove header
        array_pop($pemLines); // remove footer
        $pemContent = base64_decode(implode("\n", $pemLines));

        $derSerializer = DerPublicKeySerializer::create();
        $key = $derSerializer->parse($pemContent);
        $this->assertInstanceOf(PublicKeyInterface::class, $key);
    }

    public function testDERPublicKeySerializerConsistent()
    {
        $adapter = EccFactory::getAdapter();
        $G = EccFactory::getNistCurves($adapter)->generator192();
        $pubkey = $G->createPrivateKey()->getPublicKey();

        $serializer = DerPublicKeySerializer::create();
        $serialized = $serializer->serialize($pubkey);
        $parsed = $serializer->parse($serialized);
        $this->assertTrue($pubkey->getPoint()->equals($parsed->getPoint()));
        $this->assertEquals($pubkey->getCurve(), $parsed->getCurve());
        $this->assertEquals($pubkey->getGenerator(), $parsed->getGenerator());
    }

    /**
     * This unit test was taken over from the original authors.
     * However, optimally the {@link DerSignatureSerializer} should be analogue to the {@link DerPublicKeySerializer} tests.
     * Preferable is the way the {@link DerPublicKeySerializer} is tested: Read from content produced by trusted library, then do a consistency check.
     * As these serializers are only relevant for the test itself, testing these serializers has comparatively lower priority.
     */
    public function testDERSignatureSerializerWrites()
    {
        $r = gmp_init('15012732708734045374201164973195778115424038544478436215140305923518805725225', 10);
        $s = gmp_init('32925333523544781093325025052915296870609904100588287156912210086353851961511', 10);
        $expected = strtolower('304402202130E7D504C4A498C3B3C7C0FED6DE2A84811A3BD89BADB8627658F2B1EA5029022048CB1410308E3EFC512B4CE0974F6D0869E9454095C8855ABEA6B6325A40D0A7');
        $signature = new Signature($r, $s);
        $serializer = new DerSignatureSerializer();
        $serialized = bin2hex($serializer->serialize($signature));
        $this->assertEquals($expected, $serialized);
    }

    public function testDERSignatureSerializerConsistent()
    {
        $math = new GmpMath();
        $rbg = RandomGeneratorFactory::getRandomGenerator();
        $serializer = new DerSignatureSerializer();

        $i = 256;
        $max = $math->sub($math->pow(gmp_init(2, 10), $i), gmp_init(1, 10));
        $r = $rbg->generate($max);
        $s = $rbg->generate($max);
        $signature = new Signature($r, $s);

        $serialized = $serializer->serialize($signature);
        $parsed = $serializer->parse($serialized);

        $this->assertTrue($math->equals($signature->getR(), $parsed->getR()));
        $this->assertTrue($math->equals($signature->getS(), $parsed->getS()));
    }
}
