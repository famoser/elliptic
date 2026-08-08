<?php

namespace Famoser\Elliptic\Tests\Unit\Math\Traits;

trait AssertGmpFields
{
    /**
     * Compare all GMP fields of two coordinate objects
     */
    private function assertGMPFieldsEqual(object $expected, object $actual): void
    {
        $reflection1 = new \ReflectionClass($expected);

        $properties = $reflection1->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $val1 = $property->getValue($expected);
            $val2 = $property->getValue($actual);
            $this->assertEquals(0, gmp_cmp($val1, $val2));
        }
    }
}
