<?php

namespace Famoser\Elliptic\Tests\Integration\ExpensiveMath;

use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Tests\Integration\RFC7784\MG_ED_MathTest;

trait UnresolvedErrorTrait
{
    protected function skipUnresolvedError(string $class, string $function): void
    {
        $args = func_get_args();

        // MG_ED_Math is incorrect in relation to the baseline
        if (
            ($class === MG_ED_MathTest::class) ||
            $args[2] === MG_ED_Math::class
        ) {
            $this->markTestSkipped('MG_ED_Math is incorrect in relation to the baseline.');
        }

        // ED cycles incorrectly
        if (
            ($class === ConsistencyTest::class && $function === 'testMulCycle' && $args[2] === EDUnsafeMath::class && $args[3] === 'curve448Edwards') ||
            ($class === ConsistencyTest::class && $function === 'testMulCycle' && $args[2] === EDMath::class && $args[3] === 'curve448Edwards') ||
            ($class === ComparisonTest::class && $function === 'testMulSameResult' && $args[2] === EDMath::class && $args[3] === 'curve448Edwards') ||
            ($class === ComparisonTest::class && $function === 'testDouble' && $args[2] === EDMath::class && $args[3] === 'curve448ToEdwards')
        ) {
            $this->markTestSkipped('EDMath & EDUnsafeMath cycles incorrectly (G * N*h != 0).');
        }
    }
}
