<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Integration\Utils\DER;

use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Integration\Utils\OID\CurveOidMapper;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\Serializer\Point\ChainedPointSerializer;
use Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Sop\ASN1\Type\Constructed\Sequence;
use Sop\ASN1\Type\Primitive\BitString;
use Sop\ASN1\Type\Primitive\ObjectIdentifier;
use Sop\ASN1\Type\UnspecifiedType;
use function hex2bin;

/**
 * For testing purposes only
 * 
 * @link https://tools.ietf.org/html/rfc5480#page-3
 */
class UnsafeDerPublicKeySerializer
{
    const X509_ECDSA_OID = '1.2.840.10045.2.1';

    public function __construct(private readonly GmpMathInterface $adapter, private readonly PointSerializerInterface $pointSerializer)
    {
    }

    public static function create(): self
    {
        $adapter = MathAdapterFactory::getAdapter();
        return new self($adapter, ChainedPointSerializer::create());
    }

    public function serialize(PublicKeyInterface $key): string
    {
        $curveFp = $key->getCurve();
        if (!$curveFp instanceof NamedCurveFp) {
            throw new \RuntimeException('Not implemented for unnamed curves');
        }

        $public = $this->pointSerializer->serialize($key->getPoint());

        $sequence = new Sequence(
            new Sequence(
                new ObjectIdentifier(UnsafeDerPublicKeySerializer::X509_ECDSA_OID),
                CurveOidMapper::getCurveOid($curveFp)
            ),
            new BitString(hex2bin($public))
        );

        return $sequence->toDER();
    }

    public function parse(string $data): PublicKeyInterface
    {
        $asnObject = UnspecifiedType::fromDER($data);

        $sequence  = $asnObject->asSequence();
        $element0 = $sequence->at(0)->asSequence();
        $oid = $element0->at(0)->asObjectIdentifier();
        $curveOid = $element0->at(1)->asObjectIdentifier();
        $encodedKey = $sequence->at(1)->asBitString();

        if ($oid->oid() !== UnsafeDerPublicKeySerializer::X509_ECDSA_OID) {
            throw new \RuntimeException('Invalid data: non X509 data.');
        }

        $generator = CurveOidMapper::getGeneratorFromOid($curveOid);

        $point = $this->pointSerializer->deserialize($generator->getCurve(), bin2hex($encodedKey->string()));

        return new PublicKey($this->adapter, $generator, $point);
    }
}
