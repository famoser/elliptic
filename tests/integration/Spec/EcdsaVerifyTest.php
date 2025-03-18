<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Integration\Utils\Signature\Signature;
use Mdanter\Ecc\Integration\Utils\Signature\Signer;
use Mdanter\Ecc\Integration\Utils\Signature\SignHasher;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use PHPUnit\Framework\TestCase;

class EcdsaVerifyTest extends TestCase
{
    public function getEcdsaSignFixtures(): array
    {
        $files = FixturesRepository::read('ecdsa-verify');
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
                    gmp_init($fixture['x'], 16),
                    gmp_init($fixture['y'], 16),
                    gmp_init($fixture['r'], 16),
                    gmp_init($fixture['s'], 16),
                    $message,
                    $fixture['result']
                ];
            }
        }

        return $datasets;
    }


    /**
     * @dataProvider getEcdsaSignFixtures
     */
    public function testEcdsaSign(GeneratorPoint $G, \GMP $x, \GMP $y, \GMP $eR, \GMP $eS, \GMP $message, bool $expectedResult)
    {
        $math = $G->getAdapter();
        $signer = new Signer($math);

        $publicKey = $G->getPublicKeyFrom($x, $y);
        $signature = new Signature($eR, $eS);

        $result = $signer->verify($publicKey, $signature, $message);
        $this->assertEquals($expectedResult, $result);
    }
}
