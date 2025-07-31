<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\Utils\EdDSA;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use PHPUnit\Framework\TestCase;

class EdDSASignerTest extends TestCase
{
    public function testVerifyEd25519(): void
    {
        $public = '3d4017c3e843895a92b70aa74d1b7ebc9c982ccf2ec4968cc0cd55f12af4660c';
        $msg = '72';
        $signature = '92a009a9f0d4cab8720e820b5f642540a2b27b5416503f8fb3762223ebdb69da085ac1e43e15996e458f3613d0f11d8c387b2eaeb4302aeeb00d291612bb0c00';

        $math = new TwEDUnsafeMath(BernsteinCurveFactory::edwards25519());
        $signer = new EdDSASignerEd25519($math);

        $this->assertTrue($signer->verify($public, $signature, $msg));
    }

    public function testVerifyEd448(): void
    {
        $public = '43ba28f430cdff456ae531545f7ecd0ac834a55d9358c0372bfa0c6c6798c0866aea01eb00742802b8438ea4cb82169c235160627b4c3a9480';
        $msg = '03';
        $signature = '26b8f91727bd62897af15e41eb43c377efb9c610d48f2335cb0bd0087810f4352541b143c4b981b7e18f62de8ccdf633fc1bf037ab7cd779805e0dbcc0aae1cbcee1afb2e027df36bc04dcecbf154336c19f0af7e0a6472905e799f1953d2a0ff3348ab21aa4adafd1d234441cf807c03a00';

        $math = new EDUnsafeMath(BernsteinCurveFactory::edwards448());
        $signer = new EDDSASignerEd448($math);

        $this->assertTrue($signer->verify($public, $signature, $msg));
    }
}
