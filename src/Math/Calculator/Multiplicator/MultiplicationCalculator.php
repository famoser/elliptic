<?php

namespace Famoser\Elliptic\Math\Calculator\Multiplicator;

/**
 * @template T
 */
trait MultiplicationCalculator
{
    /** @use CofactorMultiplicator<T> */
    use CofactorMultiplicator;
    /** @use DoubleAndAddAlwaysMultiplicator<T> */
    use DoubleAndAddAlwaysMultiplicator;
}
