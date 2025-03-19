<?php

namespace Famoser\Elliptic\Integration\WycheProof;

class WycheProofConstants
{
    const RESULT_VALID = 'valid';
    const RESULT_ACCEPTABLE = 'acceptable';
    const RESULT_INVALID = 'invalid';

    const FLAG_ADD_SUB_CHAIN = 'AddSubChain';
    const FLAG_COMPRESSED_POINT = 'CompressedPoint';
    const FLAG_INVALID_ASN = 'InvalidAsn';
    const FLAG_INVALID_PUBLIC = 'InvalidPublic';
    const FLAG_LARGE_COFACTOR = 'LargeCofactor';
    const FLAG_MODIFIED_PRIME = 'ModifiedPrime';
    const FLAG_NEGATIVE_COFACTOR = 'NegativeCofactor';
    const FLAG_UNNAMED_CURVE = 'UnnamedCurve';
    const FLAG_UNUSED_PARAM = 'UnusedParam';
    const FLAG_WEAK_PUBLIC_KEY = 'WeakPublicKey';
    const FLAG_WRONG_ORDER = 'WrongOrder';
}
