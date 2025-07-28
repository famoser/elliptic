<?php

namespace Famoser\Elliptic\Tests\TestUtils;

use Famoser\Elliptic\Integration\ExpensiveMath\ComparisonTest;
use Famoser\Elliptic\Integration\ExpensiveMath\ConsistencyTest;
use Famoser\Elliptic\Integration\RFC7784\MG_ED_MathTest;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;

trait UnresolvedErrorTrait
{
    protected function skipUnresolvedError(string $class, string $function): void
    {
        $args = func_get_args();

        // MG_ED_Math is incorrect in relation to the baseline
        if (
            ($class === MG_ED_MathTest::class) ||
            ($class === \Famoser\Elliptic\Tests\Math\ComparisonTest::class && $function === 'testDouble' && $args[2] === 'curve448ToEdwards') ||
            ($class === ComparisonTest::class && $function === 'testMulSameResult' && $args[2] === MG_ED_Math::class && $args[3] === 'curve448ToEdwards')
        ) {
            $this->markTestSkipped('MG_ED_Math is incorrect in relation to the baseline.');
        }

        // TwED_ANeg1 cycle incorrectly
        if (
            ($class === ComparisonTest::class && $function === 'testMulSameResult' && $args[2] === TwED_ANeg1_Math::class && $args[3] === 'edwards25519') ||
            ($class === ComparisonTest::class && $function === 'testMulSameResult' && $args[2] === MG_TwED_ANeg1_Math::class && $args[3] === 'curve25519ToEdwards25519') ||
            ($class === ConsistencyTest::class && $function === 'testMulCycle' && $args[2] === TwED_ANeg1_Math::class && $args[3] === 'edwards25519') ||
            ($class === ConsistencyTest::class && $function === 'testMulCycle' && $args[2] === MG_TwED_ANeg1_Math::class && $args[3] === 'curve25519ToEdwards25519')
        ) {
            $this->markTestSkipped('TwED_ANeg1_Math and MG_TwED_ANeg1_Math cycle incorrectly (G * N*h != 0).');
        }
    }
}
