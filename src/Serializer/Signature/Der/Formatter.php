<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Signature\Der;

use Mdanter\Ecc\Crypto\Signature\SignatureInterface;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\Integer;

class Formatter
{
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function format(SignatureInterface $signature): string
    {
        $asn = new Sequence(
            new Integer(gmp_strval($signature->getR(), 10)),
            new Integer(gmp_strval($signature->getS(), 10))
        );

        return $asn->toDER();
    }
}
