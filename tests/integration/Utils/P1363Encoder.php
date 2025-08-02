<?php

namespace Famoser\Elliptic\Tests\Integration\Utils;

use Famoser\Elliptic\Primitives\Curve;

class P1363Encoder
{
    private readonly int $scalarOctetLength;
    public function __construct(Curve $curve)
    {
        $this->scalarOctetLength = (int) ceil((float) strlen(gmp_strval($curve->getN(), 2)) / 8);
    }

    public function encode(\GMP $r, \GMP $s): string
    {
        $rString = str_pad(gmp_strval($r, 16), $this->scalarOctetLength * 2, '0', STR_PAD_LEFT);
        $sString = str_pad(gmp_strval($s, 16), $this->scalarOctetLength * 2, '0', STR_PAD_LEFT);

        return $rString . $sString;
    }

    public function tryDecode(string $signature, \GMP &$r, \GMP &$s): bool
    {
        // crude signature validity check, as this is not our prime concern here
        if (strlen($signature) !== $this->scalarOctetLength * 4) {
            return false;
        }

        // unserialize signature
        $r = gmp_init(substr($signature, 0, $this->scalarOctetLength * 2), 16);
        $s = gmp_init(substr($signature, $this->scalarOctetLength * 2), 16);

        return true;
    }
}
