<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Integration\Utils\Signature\Signer;
use Mdanter\Ecc\Integration\Utils\Signature\SignHasher;
use Mdanter\Ecc\Legacy\Curves\CurveFactory;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use PHPUnit\Framework\TestCase;

class EcdsaSignTest extends TestCase
{
    public function getEcdsaSignFixtures(): array
    {
        $files = FixturesRepository::read('ecdsa');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['curve']);
            foreach ($file['fixtures'] as $i => $fixture) {
                $datasetIdentifier = $file['file'] . "." . $i;

                $plaintextSet = array_key_exists('msg_full', $fixture) && array_key_exists('algo', $fixture);
                $hashSet = array_key_exists('msg', $fixture);
                if ((!$plaintextSet && !$hashSet) || ($hashSet && $plaintextSet)) {
                    throw new \RuntimeException("Defined EITHER the raw hash value (msg), or the plain and hash algorithm (msg_full and algo). dataset: ". $datasetIdentifier);
                }

                $message = $hashSet ? gmp_init($fixture['msg'], 16) : null;
                if ($plaintextSet) {
                    $msg = hex2bin($fixture['msg_full']);
                    $hasher = new SignHasher($fixture['algo']);
                    $message = $hasher->makeHash($msg, $generator);
                }


                $datasets[$datasetIdentifier] = [
                    $generator,
                    gmp_init($fixture['private'], 10),
                    gmp_init($fixture['k'], 16),
                    gmp_init($fixture['r'], 16),
                    gmp_init($fixture['s'], 16),
                    $message,
                ];
            }
        }

        return $datasets;
    }


    /**
     * @dataProvider getEcdsaSignFixtures
     */
    public function testEcdsaSign(GeneratorPoint $G, \GMP $privateKeyMultiplier, \GMP $k, \GMP $eR, \GMP $eS, \GMP $message)
    {
        $math = $G->getAdapter();
        $signer = new Signer($math);

        $privateKey = $G->getPrivateKeyFrom($privateKeyMultiplier);
        $sig = $signer->sign($privateKey, $message, $k);

        $this->assertEquals($eR, $sig->getR());
        $this->assertEquals($eS, $sig->getS());

        $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $message));
    }
}
