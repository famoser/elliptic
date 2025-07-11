<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\WycheProof;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Math\MGUnsafeMath;
use PHPUnit\Framework\TestCase;

class XdhTest extends TestCase
{
    public static function provideCurve25519(): array
    {
        return FixturesRepository::createFilteredXdhFixtures('x25519');
    }

    /**
     * @dataProvider provideCurve25519
     */
    public function testCurve25519(string $comment, string $public, string $private, string $shared, string $result, array $flags): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $math = new MGUnsafeMath($curve);
        $this->testCurve($math, $comment, $public, $private, $shared, $result, $flags);
    }
}
