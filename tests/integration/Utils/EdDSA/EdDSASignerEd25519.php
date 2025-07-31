<?php

declare(strict_types=1);

namespace Famoser\Elliptic\Integration\Utils\EdDSA;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;

/**
 * implements Ed25519 according to RFC8032
 *
 * notably follows closely https://datatracker.ietf.org/doc/html/rfc8032#section-6
 */
class EdDSASignerEd25519 extends AbstractEdDSASigner
{
    public function __construct(MathInterface $math)
    {
        parent::__construct($math, new TwEDPointDecoder($math->getCurve()), 32);
    }

    protected function hash(string $hex): string
    {
        $content = hex2bin($hex);
        return hash('sha512', $content);
    }
}
