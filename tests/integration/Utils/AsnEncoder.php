<?php

namespace Famoser\Elliptic\Tests\Integration\Utils;

use Sop\ASN1\Type\UnspecifiedType;

class AsnEncoder
{
    public function decodePublicKey(string $derHex): string
    {
        $asnObject = UnspecifiedType::fromDER(hex2bin($derHex));
        $encodedKey = $asnObject->asSequence()->at(1)->asBitString();

        return bin2hex($encodedKey->string());
    }
}
