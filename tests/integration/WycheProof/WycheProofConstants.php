<?php

namespace Famoser\Elliptic\Integration\WycheProof;

class WycheProofConstants
{
    public const RESULT_VALID = 'valid';
    public const RESULT_ACCEPTABLE = 'acceptable';
    public const RESULT_INVALID = 'invalid';

    public const FLAG_ADD_SUB_CHAIN = 'AddSubChain';
    public const FLAG_COMPRESSED_POINT = 'CompressedPoint';
    public const FLAG_INVALID_ASN = 'InvalidAsn';
    public const FLAG_INVALID_PUBLIC = 'InvalidPublic';
    public const FLAG_LARGE_COFACTOR = 'LargeCofactor';
    public const FLAG_MODIFIED_PRIME = 'ModifiedPrime';
    public const FLAG_NEGATIVE_COFACTOR = 'NegativeCofactor';
    public const FLAG_UNNAMED_CURVE = 'UnnamedCurve';
    public const FLAG_UNUSED_PARAM = 'UnusedParam';
    public const FLAG_WEAK_PUBLIC_KEY = 'WeakPublicKey';
    public const FLAG_WRONG_ORDER = 'WrongOrder';
}
