<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use PHPUnit\Framework\TestCase;

class HmacTest extends TestCase
{
    /**
     * @return array
     */
    public function getHmacTestSet(): array
    {
        $files = FixturesRepository::read('hmac');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['curve']);
            foreach ($file['fixtures'] as $i => $fixture) {
                $datasetIdentifier = $file['file'] . "." . $i;

                $datasets[$datasetIdentifier] = [
                    $generator,
                    gmp_init($fixture['key'], 16),
                    $fixture['algo'],
                    $fixture['message'],
                    gmp_init($fixture['k'], 16),
                    gmp_init($fixture['r'], 16),
                    gmp_init($fixture['s'], 16)
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getHmacTestSet
     */
    public function testHmacSignatures(GeneratorPoint $G, \GMP $key, string $hashAlgorithm, string $message, \GMP $eK, \GMP $eR, \GMP $eS)
    {
        $math = $G->getAdapter();

        $privateKey = $G->getPrivateKeyFrom($key);
        $signer = new Signer($math);
        $hasher = new SignHasher($hashAlgorithm);
        $hashDec = $hasher->makeHash($message, $G);

        $hmac = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $hashDec, $hashAlgorithm);
        $k = $hmac->generate($G->getOrder());
        $this->assertEquals($eK, $k);

        $sig = $signer->sign($privateKey, $hashDec, $k);

        $this->assertEquals($eR, $sig->getR());
        $this->assertEquals($eS, $sig->getS());

        $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $hashDec));
    }
}
