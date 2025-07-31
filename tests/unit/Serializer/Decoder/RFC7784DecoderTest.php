<?php

namespace Famoser\Elliptic\Tests\Serializer\Decoder;

use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use PHPUnit\Framework\TestCase;

class RFC7784DecoderTest extends TestCase
{
    public function test25519(): void
    {
        // values from https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 (X25519, 1/2)
        $scalarHex1 = 'a546e36bf0527c9d3b16154b82465edd62144c0ac1fc5a18506a2244ba449ac4';
        $expectedScalar1 = gmp_init('31029842492115040904895560451863089656
     472772604678260265531221036453811406496', 10);
        $uHex1 = 'e6db6867583030db3594c1a424b15f7c726624ec26b3353b10a903a6d0ab1c4c';
        $expectedU1 = gmp_init('34426434033919594451155107781188821651
     316167215306631574996226621102155684838', 10);
        $encoder = new RFC7784Decoder();
        $this->assertEquals(0, gmp_cmp($encoder->decodeScalar25519($scalarHex1), $expectedScalar1));
        $this->assertEquals(0, gmp_cmp($encoder->decodeUCoordinate($uHex1, 255), $expectedU1));

        // values from https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 (X25519, 2/2)
        $scalarHex2 = '4b66e9d4d1b4673c5ad22691957d6af5c11b6421e0ea01d42ca4169e7918ba0d';
        $expectedScalar2 = gmp_init('35156891815674817266734212754503633747
     128614016119564763269015315466259359304', 10);
        $uHex2 = 'e5210f12786811d3f4b7959d0538ae2c31dbe7106fc03c3efc4cd549c715a493';
        $expectedU2 = gmp_init('88838573511839298940907593866106493194
     17338800022198945255395922347792736741', 10);
        $encoder = new RFC7784Decoder();
        $this->assertEquals(0, gmp_cmp($encoder->decodeScalar25519($scalarHex2), $expectedScalar2));
        $this->assertEquals(0, gmp_cmp($encoder->decodeUCoordinate($uHex2, 255), $expectedU2));
    }

    public function test25519Encode(): void
    {
        // values from https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 (X25519, 1/2)
        $u = gmp_init('34426434033919594451155107781188821651
     316167215306631574996226621102155684838', 10);
        $expectedUHex = 'e6db6867583030db3594c1a424b15f7c726624ec26b3353b10a903a6d0ab1c4c';

        $encoder = new RFC7784Decoder();
        $uHex = $encoder->encodeUCoordinate($u, 255);
        $this->assertEquals($expectedUHex, $uHex);
    }

    public function test448(): void
    {
        // values from https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 (X448, 1/2)
        $scalarHex1 = '3d262fddf9ec8e88495266fea19a34d28882acef045104d0d1aae121' .
            '700a779c984c24f8cdd78fbff44943eba368f54b29259a4f1c600ad3';
        $expectedScalar1 = gmp_init('599189175373896402783756016145213256157230856
     085026129926891459468622403380588640249457727
     683869421921443004045221642549886377526240828', 10);
        $uHex1 = '06fce640fa3487bfda5f6cf2d5263f8aad88334cbd07437f020f08f9' .
            '814dc031ddbdc38c19c6da2583fa5429db94ada18aa7a7fb4ef8a086';
        $expectedU1 = gmp_init('382239910814107330116229961234899377031416365
     240571325148346555922438025162094455820962429
     142971339584360034337310079791515452463053830', 10);
        $encoder = new RFC7784Decoder();
        $this->assertEquals(0, gmp_cmp($encoder->decodeScalar448($scalarHex1), $expectedScalar1));
        $this->assertEquals(0, gmp_cmp($encoder->decodeUCoordinate($uHex1, 448), $expectedU1));

        // values from https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 (X448, 2/2)
        $scalarHex2 = '203d494428b8399352665ddca42f9de8fef600908e0d461cb021f8c5' .
            '38345dd77c3e4806e25f46d3315c44e0a5b4371282dd2c8d5be3095f';
        $expectedScalar2 = gmp_init('633254335906970592779259481534862372382525155
     252028961056404001332122152890562527156973881
     968934311400345568203929409663925541994577184', 10);
        $uHex2 = '0fbcc2f993cd56d3305b0b7d9e55d4c1a8fb5dbb52f8e9a1e9b6201b' .
            '165d015894e56c4d3570bee52fe205e28a78b91cdfbde71ce8d157db';
        $expectedU2 = gmp_init('622761797758325444462922068431234180649590390
     024811299761625153767228042600197997696167956
     134770744996690267634159427999832340166786063', 10);
        $encoder = new RFC7784Decoder();
        $this->assertEquals(0, gmp_cmp($encoder->decodeScalar448($scalarHex2), $expectedScalar2));
        $this->assertEquals(0, gmp_cmp($encoder->decodeUCoordinate($uHex2, 448), $expectedU2));
    }

    public function test448Encode(): void
    {
        // values from https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 (X25519, 2/2)
        $u = gmp_init('382239910814107330116229961234899377031416365
     240571325148346555922438025162094455820962429
     142971339584360034337310079791515452463053830', 10);
        $expectedUHex = '06fce640fa3487bfda5f6cf2d5263f8aad88334cbd07437f020f08f9814dc031ddbdc38c19c6da2583fa5429db94ada18aa7a7fb4ef8a086';

        $encoder = new RFC7784Decoder();
        $uHex = $encoder->encodeUCoordinate($u, 448);
        $this->assertEquals($expectedUHex, $uHex);
    }
}
