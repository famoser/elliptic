<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Tests\Integration\Utils\EdDSA;

use danielburger1337\SHA3Shake\SHA3Shake;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Serializer\PointDecoder\EDPointDecoder;

/**
 * implements Ed448 according to RFC8032
 *
 * notably follows closely https://datatracker.ietf.org/doc/html/rfc8032#section-6
 */
class EDDSASignerEd448 extends AbstractEdDSASigner
{
    public function __construct(MathInterface $math)
    {
        parent::__construct($math, new EDPointDecoder($math->getCurve()), 57);
    }

    protected function hash(string $hex): string
    {
        // hardcode that there is no context and no flag
        $dompfx = bin2hex("SigEd448") . "00" . "00";

        $content = hex2bin($dompfx . $hex);
        return SHA3Shake::shake256($content, 114 * 2);
    }
}
