<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Legacy\Serializer\Point;

use Mdanter\Ecc\Legacy\Primitives\CurveFpInterface;
use Mdanter\Ecc\Legacy\Primitives\PointInterface;

interface PointSerializerInterface
{
    public function serialize(PointInterface $point): string;

    public function deserialize(CurveFpInterface $curve, string $point): PointInterface;

    public function supportsDeserialize(string $point): bool;
}
