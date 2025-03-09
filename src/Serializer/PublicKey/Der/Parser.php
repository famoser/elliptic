<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\PublicKey\Der;

use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Sop\ASN1\Type\UnspecifiedType;

class Parser
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var UncompressedPointSerializer
     */
    private $pointSerializer;

    /**
     * Parser constructor.
     * @param GmpMathInterface $adapter
     * @param PointSerializerInterface|null $pointSerializer
     */
    public function __construct(GmpMathInterface $adapter, PointSerializerInterface $pointSerializer = null)
    {
        $this->adapter = $adapter;
        $this->pointSerializer = $pointSerializer ?: new UncompressedPointSerializer();
    }

    /**
     * @param string $binaryData
     * @return PublicKeyInterface
     */
    public function parse(string $binaryData): PublicKeyInterface
    {
        $asnObject = UnspecifiedType::fromDER($binaryData);

        $sequence  = $asnObject->asSequence();
        $element0 = $sequence->at(0)->asSequence();
        $oid = $element0->at(0)->asObjectIdentifier();
        $curveOid = $element0->at(1)->asObjectIdentifier();
        $encodedKey = $sequence->at(1)->asBitString();

        if ($oid->oid() !== DerPublicKeySerializer::X509_ECDSA_OID) {
            throw new \RuntimeException('Invalid data: non X509 data.');
        }

        $generator = CurveOidMapper::getGeneratorFromOid($curveOid);

        return $this->parseKey($generator, $encodedKey->string());
    }

    /**
     * @param GeneratorPoint $generator
     * @param string $data
     * @return PublicKeyInterface
     */
    public function parseKey(GeneratorPoint $generator, string $data): PublicKeyInterface
    {
        $point = $this->pointSerializer->unserialize($generator->getCurve(), bin2hex($data));

        return new PublicKey($this->adapter, $generator, $point);
    }
}
