<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Legacy\Exception;

use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Mdanter\Ecc\Legacy\Primitives\PointInterface;
use Throwable;

class PublicKeyException extends \RuntimeException
{
    /**
     * @var GeneratorPoint
     */
    private $G;

    /**
     * @var PointInterface
     */
    private $point;

    public function __construct(GeneratorPoint $G, PointInterface $point, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->G = $G;
        $this->point = $point;
        parent::__construct($message, $code, $previous);
    }

    public function getGenerator(): GeneratorPoint
    {
        return $this->G;
    }

    public function getPoint(): PointInterface
    {
        return $this->point;
    }
}
