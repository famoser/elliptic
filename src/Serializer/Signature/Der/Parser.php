<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature\Der;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Sop\ASN1\Type\UnspecifiedType;

class Parser
{
    /**
     * @param string $binary
     * @return SignatureInterface
     */
    public function parse(string $binary): SignatureInterface
    {
        $asnObject = UnspecifiedType::fromDER($binary);

        $sequence = $asnObject->asSequence();
        $r = $sequence->at(0)->asInteger();
        $s = $sequence->at(1)->asInteger();

        return new Signature(
            gmp_init($r->number(), 10),
            gmp_init($s->number(), 10)
        );
    }
}
