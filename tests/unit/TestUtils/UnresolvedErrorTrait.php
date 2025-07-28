<?php

namespace Famoser\Elliptic\Tests\TestUtils;

use Famoser\Elliptic\Tests\Math\MathComparisonTest;

trait UnresolvedErrorTrait
{
    private function skipUnresolvedError(string $class, string $method): void
    {
        $args = func_get_args();
        if ($class === MathComparisonTest::class && $method === 'testDouble' && $args[2] === 'curve448ToEdwards') {
            $this->markTestSkipped('MG_ED_Math and MGUnsafeMath have different add/double results.');
        }
    }
}
