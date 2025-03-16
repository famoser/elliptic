<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\WycheProof;

use Mdanter\Ecc\Curves\SecpCurves;

class EcdhSepkEcpointTest extends AbstractEcdhTest
{
    private function getFixtures(string $testcase): array
    {
        $curve = str_replace('testSecp', 'secp', $testcase);
        return FixturesRepository::createEcdhEcpointFixtures($curve);
    }

    /**
     * @dataProvider getFixtures
     */
    public function testSecp224r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $generator = SecpCurves::create()->generator224r1();
        $this->testCurve($generator, $comment, $public, $private, $shared, $result, $flags);
    }

    /**
     * @dataProvider getFixtures
     */
    public function testSecp256r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $generator = SecpCurves::create()->generator256r1();
        $this->testCurve($generator, $comment, $public, $private, $shared, $result, $flags);
    }

    /**
     * @dataProvider getFixtures
     */
    public function testSecp384r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $generator = SecpCurves::create()->generator384r1();
        $this->testCurve($generator, $comment, $public, $private, $shared, $result, $flags);
    }

    /**
     * @dataProvider getFixtures
     */
    public function testSecp521r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $generator = SecpCurves::create()->generator521r1();
        $this->testCurve($generator, $comment, $public, $private, $shared, $result, $flags);
    }
}
