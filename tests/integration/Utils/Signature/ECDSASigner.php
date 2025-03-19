<?php

declare(strict_types=1);

namespace Mdanter\Ecc\Integration\Utils\Signature;

use Mdanter\Ecc\Legacy\Util\NumberSize;
use Mdanter\Ecc\Math\MathInterface;
use Mdanter\Ecc\Primitives\Point;

class ECDSASigner
{
    public function __construct(private readonly MathInterface $math, private readonly string $hashAlgorithm = 'sha256')
    {
    }

    private function hash(string $message): \GMP
    {
        $hashValue = hash($this->hashAlgorithm, $message);
        $hashBits = gmp_strval(gmp_init($hashValue, 16), 2);

        // expand to fill out all bits
        $expectedHashBits = (int) substr($this->hashAlgorithm, 3);
        $hashBits = str_pad($hashBits, $expectedHashBits, '0', STR_PAD_LEFT);

        // cut out lower bits that do not fit inside the curve order
        $truncateSize = strlen(gmp_strval($this->math->getCurve()->getN(), 2));
        $truncatedHash = substr($hashBits, 0, $truncateSize);

        return gmp_init($truncatedHash, 2);
    }

    private function tryDecodeSignature(string $signature, \GMP &$r, \GMP &$s): bool
    {
        // crude signature validity check, as this is not our prime concern here
        $integerOctetLength = (int) ceil((float) strlen(gmp_strval($this->math->getCurve()->getN(), 2)) / 8);
        if (strlen($signature) !== $integerOctetLength*4) {
            return false;
        }

        // unserialize signature
        $r = gmp_init(substr($signature, 0, $integerOctetLength*2), 16);
        $s = gmp_init(substr($signature, $integerOctetLength*2), 16);

        return true;
    }

    public function sign(\GMP $secret, string $message, \GMP $k): Signature
    {
        $n = $this->math->getCurve()->getN();

        $G = $this->math->getCurve()->getG();
        $r = gmp_mod($this->math->mul($G, $k)->x, $n);
        if (gmp_cmp($r, 0)) {
            throw new \RuntimeException("Error: random number R = 0");
        }

        $hash = $this->hash($message);
        $inner = gmp_add($hash, gmp_mul($secret, $r));
        $s = gmp_mod(gmp_mul(gmp_invert($k, $n), $inner), $n);
        if (gmp_cmp($s, 0)) {
            throw new \RuntimeException("Error: random number S = 0");
        }

        return new Signature($r, $s);
    }

    public function verify(Point $publicKey, string $signature, string $message): bool
    {
        $n = $this->math->getCurve()->getN();
        $G = $this->math->getCurve()->getG();

        $r = gmp_init(0);
        $s = gmp_init(0);
        if (!$this->tryDecodeSignature($signature, $r, $s)) {
            return false;
        }

        $one = gmp_init(1, 10);
        if (gmp_cmp($r, $one) < 0 || gmp_cmp($r, gmp_sub($n, $one)) > 0) {
            return false;
        }

        if (gmp_cmp($s, $one) < 0 || gmp_cmp($s, gmp_sub($n, $one)) > 0) {
            return false;
        }

        $hash = $this->hash($message);
        $c = gmp_invert($s, $n);
        $u1 = gmp_mul($hash, $c);
        $u2 = gmp_mul($r, $c);
        $xy = $this->math->add($this->math->mul($G, $u1), $this->math->mul($publicKey, $u2));

        // check equality
        $result = gmp_mod(gmp_sub($xy->x, $r), $n);

        return gmp_cmp($result, 0) === 0;
    }
}
