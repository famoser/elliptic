<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\PublicKey\Der;

use Mdanter\Ecc\Primitives\PointInterface;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\BitString;
use Sop\ASN1\Type\Primitive\ObjectIdentifier;

class Formatter
{
    /**
     * @var UncompressedPointSerializer
     */
    private $pointSerializer;

    /**
     * Formatter constructor.
     * @param PointSerializerInterface|null $pointSerializer
     */
    public function __construct(PointSerializerInterface $pointSerializer = null)
    {
        $this->pointSerializer = $pointSerializer ?: new UncompressedPointSerializer();
    }

    /**
     * @param PublicKeyInterface $key
     * @return string
     */
    public function format(PublicKeyInterface $key): string
    {
        $curveFp = $key->getCurve();
        if (!$curveFp instanceof NamedCurveFp) {
            throw new \RuntimeException('Not implemented for unnamed curves');
        }

        $public = $this->pointSerializer->serialize($key->getPoint());

        $sequence = new Sequence(
            new Sequence(
                new ObjectIdentifier(DerPublicKeySerializer::X509_ECDSA_OID),
                CurveOidMapper::getCurveOid($curveFp)
            ),
            new BitString(hex2bin($public))
        );

        return $sequence->toDER();
    }
}
