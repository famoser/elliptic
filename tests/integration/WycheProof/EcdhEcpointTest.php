<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\SW_ANeg3_Math;

class EcdhEcpointTest extends AbstractEcdhTestCase
{
    public static function provideSecp256r1(): array
    {
        return FixturesRepository::createEcdhEcpointFixtures('secp256r1');
    }

    /**
     * @dataProvider provideSecp256r1
     */
    public function testSecp256r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $curve = SEC2CurveFactory::secp256r1();
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    public static function provideSecp384r1(): array
    {
        return FixturesRepository::createEcdhEcpointFixtures('secp384r1');
    }

    /**
     * @dataProvider provideSecp384r1
     */
    public function testSecp384r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $curve = SEC2CurveFactory::secp384r1();
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }

    public static function provideSecp521r1(): array
    {
        return FixturesRepository::createEcdhEcpointFixtures('secp521r1');
    }

    /**
     * @dataProvider provideSecp521r1
     */
    public function testSecp521r1(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $curve = SEC2CurveFactory::secp521r1();
        $math = new SW_ANeg3_Math($curve);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }
}
