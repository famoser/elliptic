<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Integration\Utils\Signature;

class Signature
{
    public function __construct(private \GMP $r, private readonly \GMP $s)
    {
    }

    public function getR(): \GMP
    {
        return $this->r;
    }

    public function getS(): \GMP
    {
        return $this->s;
    }
}
