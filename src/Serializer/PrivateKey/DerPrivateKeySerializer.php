<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\PrivateKey;

use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Serializer\Point\ChainedPointSerializer;
use Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\BitString;
use Sop\ASN1\Type\Primitive\Integer;
use Sop\ASN1\Type\Primitive\OctetString;
use Sop\ASN1\Type\Tagged\ExplicitlyTaggedType;
use Sop\ASN1\Type\UnspecifiedType;

/**
 * PEM Private key formatter
 *
 * @link https://tools.ietf.org/html/rfc5915
 */
class DerPrivateKeySerializer
{
    const VERSION = 1;

    public function __construct(private readonly PointSerializerInterface $pointSerializer)
    {
    }

    public static function create(): self
    {
        return new self(ChainedPointSerializer::create());
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface::serialize()
     */
    public function serialize(PrivateKeyInterface $key): string
    {
        $curveFp = $key->getPoint()->getCurve();
        if (!$curveFp instanceof NamedCurveFp) {
            throw new \RuntimeException('Not implemented for unnamed curves');
        }

        $secret = gmp_strval($key->getSecret(), 16);
        $public = $this->pointSerializer->serialize($key->getPublicKey()->getPoint());

        $privateKeyInfo = new Sequence(
            new Integer(self::VERSION),
            new OctetString(hex2bin($secret)),
            new ExplicitlyTaggedType(0, CurveOidMapper::getCurveOid($curveFp)),
            new ExplicitlyTaggedType(1, new BitString(hex2bin($public)))
        );

        return $privateKeyInfo->toDER();
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface::parse()
     */
    public function parse(string $data): PrivateKeyInterface
    {
        $asnObject = UnspecifiedType::fromDER($data);
        $sequence = $asnObject->asSequence();

        $version = $sequence->at(0);

        if ($version->asInteger()->intNumber() != 1) {
            throw new \RuntimeException('Invalid data: only version 1 (RFC5915) keys are supported.');
        }

        $key = $sequence->at(1)->asOctetString()->string();
        $oid = $sequence->at(2)->asTagged()->asExplicit(0)->asObjectIdentifier();
        $generator = CurveOidMapper::getGeneratorFromOid($oid);

        $gmpKey = gmp_init(bin2hex($key), 16);
        return $generator->getPrivateKeyFrom($gmpKey);
    }
}
