<?php

namespace Mdanter\Ecc\Curves;

use Mdanter\Ecc\Primitives\Curve;

/**
 * Resolves to all known curves, guaranteeing a single instance per curve.
 */
class CurveRepository
{
    /**
     * @var array<string, string>
     */
    private array $oidAlias = [
        /* source: https://www.secg.org/sec2-v2.pdf A.2.1 */
        '1.3.132.0.31' => 'secp192k1',
        '1.2.840.10045.3.1.1' => 'secp192r1',
        '1.3.132.0.32' => 'secp224k1',
        '1.3.132.0.33' => 'secp224r1',
        '1.3.132.0.10' => 'secp256k1',
        '1.2.840.10045.3.1.7' => 'secp256r1',
        '1.3.132.0.34' => 'secp384r1',
        '1.3.132.0.35' => 'secp521r1'
    ];

    /**
     * @var array<string, string>
     */
    private array $nameAlias = [
        /* source: https://www.secg.org/sec2-v2.pdf A.2.1 */
        'prime192v1' => 'secp192r1',
        'prime256v1' => 'secp256r1',

        /* source: https://www.gnupg.org/documentation/manuals/gcrypt/ECC-key-parameters.html */
        'NIST P-192' => 'secp192r1',
        'nistp192' => 'secp192r1',
        'NIST P-224' => 'secp224r1',
        'nistp224' => 'secp224r1',
        'NIST P-256' => 'secp256r1',
        'nistp256' => 'secp256r1',
        'NIST P-384' => 'secp384r1',
        'nistp384' => 'secp384r1',
        'NIST P-521' => 'secp521r1',
        'nistp521' => 'secp521r1',
    ];

    /**
     * @var array<string, array{0: class-string, 1: string}>
     */
    private array $canonicalNameCurveConstructors = [
        'secp192k1' => [SEC2CurveFactory::class, 'secp192k1'],
        'secp192r1' => [SEC2CurveFactory::class, 'secp192r1'],
        'secp224k1' => [SEC2CurveFactory::class, 'secp224k1'],
        'secp224r1' => [SEC2CurveFactory::class, 'secp224r1'],
        'secp256k1' => [SEC2CurveFactory::class, 'secp256k1'],
        'secp256r1' => [SEC2CurveFactory::class, 'secp256r1'],
        'secp384r1' => [SEC2CurveFactory::class, 'secp384r1'],
        'secp521r1' => [SEC2CurveFactory::class, 'secp521r1'],
    ];

    /**
     * @var array<string, Curve>
     */
    private array $curveCache = [];

    /**
     * @return string[]
     */
    public function getKnownNames(): array
    {
        return array_merge(array_keys($this->canonicalNameCurveConstructors), array_keys($this->nameAlias));
    }

    /**
     * @return string[]
     */
    public function getKnownCurveOIDs(): array
    {
        return array_keys($this->oidAlias);
    }

    public function findByOID(string $oid): ?Curve
    {
        if (!array_key_exists($oid, $this->oidAlias)) {
            return null;
        }

        $canonicalName = $this->oidAlias[$oid];
        return $this->findByCanonicalName($canonicalName);
    }

    public function findByName(string $name): ?Curve
    {
        $canonicalName = array_key_exists($name, $this->nameAlias) ? $this->nameAlias[$name] : $name;

        return $this->findByCanonicalName($canonicalName);
    }

    private function findByCanonicalName(string $canonicalName): ?Curve
    {
        if (!array_key_exists($canonicalName, $this->canonicalNameCurveConstructors)) {
            return null;
        }

        if (!array_key_exists($canonicalName, $this->curveCache)) {
            $curveConstructor = $this->canonicalNameCurveConstructors[$canonicalName];
            $this->curveCache[$canonicalName] = $curveConstructor();
        }

        return $this->curveCache[$canonicalName];
    }
}
