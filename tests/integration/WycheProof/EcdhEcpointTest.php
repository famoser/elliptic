<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Integration\WycheProof\Traits\DiffieHellmanTrait;
use Famoser\Elliptic\Integration\WycheProof\Traits\EncodedPointTrait;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use PHPUnit\Framework\TestCase;

class EcdhEcpointTest extends TestCase
{
    use EncodedPointTrait;
    use DiffieHellmanTrait;

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

    private function testCurve(MathInterface $math, string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $this->assertSWPublicKeyDeserializes($math, $comment, $public, $result, $flags, $publicKeyPoint);
        $this->assertDHCorrect($math, $publicKeyPoint, gmp_init($private, 16), gmp_init($shared, 16), $result);
    }
}
