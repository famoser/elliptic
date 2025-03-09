<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Point;

use Mdanter\Ecc\Primitives\PointInterface;
use Mdanter\Ecc\Primitives\CurveFpInterface;

interface PointSerializerInterface
{
    public function serialize(PointInterface $point): string;

    public function deserialize(CurveFpInterface $curve, string $point): PointInterface;

    public function supportsDeserialize(string $point): bool;
}
