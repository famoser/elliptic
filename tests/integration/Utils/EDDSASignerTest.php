<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\Utils;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use PHPUnit\Framework\TestCase;

class EDDSASignerTest extends TestCase
{
    public function testVerify()
    {
        $public = '3d4017c3e843895a92b70aa74d1b7ebc9c982ccf2ec4968cc0cd55f12af4660c';
        $msg = '72';
        $signature = '92a009a9f0d4cab8720e820b5f642540a2b27b5416503f8fb3762223ebdb69da085ac1e43e15996e458f3613d0f11d8c387b2eaeb4302aeeb00d291612bb0c00';

        $math = new TwEDUnsafeMath(BernsteinCurveFactory::edwards25519());
        $signer = new EDDSASigner($math);

        $this->assertTrue($signer->verify($public, $signature, $msg));
    }
}
