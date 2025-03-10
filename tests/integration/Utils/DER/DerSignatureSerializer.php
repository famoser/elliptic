<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Integration\Utils\DER;

use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\UnspecifiedType;

class DerSignatureSerializer
{
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(SignatureInterface $signature): string
    {
        $asn = new Sequence(
            new Integer(gmp_strval($signature->getR(), 10)),
            new Integer(gmp_strval($signature->getS(), 10))
        );

        return $asn->toDER();
    }

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
