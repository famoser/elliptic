<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Legacy;

use Mdanter\Ecc\Legacy\Curves\NistCurve;
use Mdanter\Ecc\Legacy\Curves\SecpCurves;
use Mdanter\Ecc\Legacy\Math\GmpMathInterface;
use Mdanter\Ecc\Legacy\Math\MathAdapterFactory;

/**
 * Static factory class providing factory methods to work with NIST and SECG recommended curves.
 */
class EccFactory
{
    /**
     * Selects and creates the most appropriate adapter for the running environment.
     *
     * @param bool $debug [optional] Set to true to get a trace of all mathematical operations
     *
     * @throws \RuntimeException
     * @return GmpMathInterface
     */
    public static function getAdapter(bool $debug = false): GmpMathInterface
    {
        return MathAdapterFactory::getAdapter($debug);
    }

    /**
     * Returns a factory to create NIST Recommended curves and generators.
     *
     * @param  GmpMathInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return NistCurve
     */
    public static function getNistCurves(GmpMathInterface $adapter = null): NistCurve
    {
        return new NistCurve($adapter ?: self::getAdapter());
    }

    /**
     * Returns a factory to return SECG Recommended curves and generators.
     *
     * @param  GmpMathInterface $adapter [optional] Defaults to the return value of EccFactory::getAdapter().
     * @return SecpCurves
     */
    public static function getSecgCurves(GmpMathInterface $adapter = null): SecpCurves
    {
        return new SecpCurves($adapter ?: self::getAdapter());
    }
}
